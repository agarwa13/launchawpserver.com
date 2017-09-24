<?php
/**
 * Created by PhpStorm.
 * User: nikhilagarwal
 * Date: 7/9/17
 * Time: 3:39 PM
 */

namespace App\Helpers;


use App\Server;
use Aws\Ec2\Ec2Client;
use Aws\Iam\IamClient;

class AWSHelpers
{

    private static function getIAMClient($access_key_id, $secret_access_key)
    {
        return new IamClient([
            'credentials' => [
                'key' => $access_key_id,
                'secret' => $secret_access_key
            ],
            'region' => 'us-west-2',
            'version' => '2010-05-08'
        ]);
    }

    /**
     * @param $access_key_id
     * @param $secret_access_key
     * @param $region
     * @return Ec2Client
     */
    private static function getEC2Client($access_key_id, $secret_access_key, $region)
    {
        return new Ec2Client([
            'credentials' => [
                'key' => $access_key_id,
                'secret' => $secret_access_key
            ],
            'region' => $region,
            'version' => '2016-11-15',
        ]);
    }


    public static function create_key_pair($server, $keyPairName)
    {

        $credentials = $server->credential;

        // Get the EC2 Client
        $client = self::getEC2Client(
            $credentials->access_key_id,
            $credentials->secret_access_key,
            $server->region
        );

        // Create the Key Pair on AWS
        $result = $client->createKeyPair(array(
            'KeyName' => $keyPairName
        ));

        return $result;
    }


    public static function create_security_group($server){

        $credentials = $server->credential;

        $client = self::getEC2Client(
            $credentials->access_key_id,
            $credentials->secret_access_key,
            $server->region
        );

        $security_group_name = 'launch-a-wp-server-'.$server->id;

        $client->createSecurityGroup(array(
            'GroupName'   => $security_group_name,
            'Description' => 'Launch a WP Server Security Group'
        ));

        // Set ingress rules for the security group
        $client->authorizeSecurityGroupIngress(array(
            'GroupName'     => $security_group_name,
            'IpPermissions' => array(
                array(
                    'IpProtocol' => 'tcp',
                    'FromPort'   => 80,
                    'ToPort'     => 80,
                    'IpRanges'   => array(
                        array('CidrIp' => '0.0.0.0/0')
                    ),
                ),
                array(
                    'IpProtocol' => 'tcp',
                    'FromPort'   => 22,
                    'ToPort'     => 22,
                    'IpRanges'   => array(
                        array('CidrIp' => '0.0.0.0/0')
                    ),
                ),
                array(
                    'IpProtocol' => 'tcp',
                    'FromPort'   => 443,
                    'ToPort'     => 443,
                    'IpRanges'   => array(
                        array('CidrIp' => '0.0.0.0/0')
                    ),
                )
            )
        ));

        return $security_group_name;

    }


    private static function getAWSServerSize($size){

        switch($size){
            case '512MB':
                return 't2.nano';
            case '1GB':
                return 't2.micro';
            case '2GB':
                return 't2.small';
            case '4GB':
                return 't2.medium';
            case '8GB':
                return 't2.large';
            case '16GB':
                return 't2.xlarge';
            default:
                return 't2.micro';
        }

    }

    public static function launchInstance( $server, $key_pair_name, $security_group_name ){

        $credentials = $server->credential;

        $client = self::getEC2Client(
            $credentials->access_key_id,
            $credentials->secret_access_key,
            $server->region
        );

        // Launch an instance with the key pair and security group
        $result = $client->runInstances(array(
//            'ImageId'        => 'ami-840910ee',
            'ImageId'        => 'ami-1e2edb64',
            'MinCount'       => 1,
            'MaxCount'       => 1,
            'InstanceType'   => self::getAWSServerSize($server->size),
            'KeyName'        => $key_pair_name,
            'SecurityGroups' => array($security_group_name),
        ));

        // Get the Instance IDs
        $instances = $result->get('Instances');
        $instance = $instances[0];
        $instance_id = $instance['InstanceId'];

        // Save the Instance ID to the Database
        $server->aws_instance_id = $instance_id;
        $server->save();

        // Update the Instance Name Tag
        $client->createTags([
           'resources' => $server->aws_instance_id,
            'key' => 'name',
            'value' => $server->name
        ]);

        return $instance;
    }


    public static function wait_until_instance_is_ready($server){

        $credentials = $server->credential;

        // Get an EC2 Client
        $client = self::getEC2Client(
            $credentials->access_key_id,
            $credentials->secret_access_key,
            $server->region
        );

        // Wait until the instance is launched
        $client->getWaiter('InstanceStatusOk',array(
            'InstanceIds' => [$server->aws_instance_id],
        ));

        // Wait a little bit more just to make sure... (Sigh! AWS)
        sleep(5);
    }

    public static function update_ip_addresses_in_database($server){

        $credentials = $server->credential;

        $client = self::getEC2Client(
            $credentials->access_key_id,
            $credentials->secret_access_key,
            $server->region
        );

        // Describe the now-running instance to get the public ip address
        $result = $client->describeInstances(array(
            'InstanceIds' => [$server->aws_instance_id],
        ));

        // Store the IP Address in the Database
        $server->ip_address = $result['Reservations'][0]['Instances'][0]['PublicIpAddress'];
        $server->private_ip_address = $result['Reservations'][0]['Instances'][0]['NetworkInterfaces'][0]['PrivateIpAddress'];
        $server->save();

    }


    public static function restart_server(Server $server){
        $credentials = $server->credential;
        $client = self::getEC2Client($credentials->access_key_id, $credentials->secret_access_key, $server->region);

        $client->rebootInstances([
            'InstanceIds' => [$server->aws_instance_id]
        ]);
    }


    public static function addAccessPolicies($credentials){

        $client = self::getIAMClient($credentials->access_key_id, $credentials->secret_access_key);

        // Get Access to Amazon EC2
        $client->attachUserPolicy([
            'PolicyArn' => 'arn:aws:iam::aws:policy/AmazonEC2FullAccess',
            'UserName' => $credentials->name
        ]);

        // Get Access to SSH Keys (to create Key Pairs)
        $client->attachUserPolicy([
            'PolicyArn' => 'arn:aws:iam::aws:policy/IAMUserSSHKeys',
            'UserName' => $credentials->name
        ]);

        // Get Access to Amazon Route 53
        $client->attachUserPolicy([
            'PolicyArn' => 'arn:aws:iam::aws:policy/AmazonRoute53FullAccess',
            'UserName' => $credentials->name
        ]);

        // Get Access to Amazon VPC
        $client->attachUserPolicy([
            'PolicyArn' => 'arn:aws:iam::aws:policy/AmazonVPCFullAccess',
            'UserName' => $credentials->name
        ]);

        // Get Access to Cloud Watch
        $client->attachUserPolicy([
            'PolicyArn' => 'arn:aws:iam::aws:policy/CloudWatchFullAccess',
            'UserName' => $credentials->name
        ]);

    }

}