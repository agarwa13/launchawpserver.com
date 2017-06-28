<?php

namespace App\Jobs;

use App\Launcher;
use Aws\Ec2\Ec2Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LaunchInstance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $launcher;

    /*
     * Set the Timeout to be 5 minutes
     */
    public $timeout = 300;

    /**
     * Create a new job instance
     * @param Launcher $launcher
     */
    public function __construct(Launcher $launcher)
    {
        $this->launcher = $launcher;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // Create an EC2 Client
        $ec2Client = Ec2Client::factory(array(
            'key'    => $this->launcher->aws_access_key_id,
            'secret' => $this->launcher->aws_secret_access_key,
            'region' => $this->launcher->region, // (e.g., us-east-1)
            'version' => '2016-11-15',
            'credentials' => array(
                'key'    => $this->launcher->aws_access_key_id,
                'secret' => $this->launcher->aws_secret_access_key
            )
        ));

        // Create a Key Pair
        $keyPairName = 'key-pair-'.$this->launcher->id;
        $result = $ec2Client->createKeyPair(array(
            'KeyName' => $keyPairName
        ));

        // Save the Private key to Disk
        $saveKeyLocation = storage_path('app/'.$keyPairName.".pem");
        file_put_contents( $saveKeyLocation ,  $result['KeyMaterial'] );

        // Save the location to Database for later retrieval using the Storage::get function
        $this->launcher->key_pair_location = $keyPairName.".pem";
        $this->launcher->save();

        // Update the key's permissions so it can be used with SSH
        chmod( $saveKeyLocation , 0600);

        // Create the Security Group
        $securityGroupName = 'bloggercasts-webserver-'.$this->launcher->id;
        $ec2Client->createSecurityGroup(array(
            'GroupName'   => $securityGroupName,
            'Description' => 'Basic web server security'
        ));

        // Set ingress rules for the security group
        $ec2Client->authorizeSecurityGroupIngress(array(
            'GroupName'     => $securityGroupName,
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

        // Launch an instance with the key pair and security group
        $result = $ec2Client->runInstances(array(
            'ImageId'        => 'ami-840910ee',
            'MinCount'       => 1,
            'MaxCount'       => 1,
            'InstanceType'   => 't2.micro',
            'KeyName'        => $keyPairName,
            'SecurityGroups' => array($securityGroupName),
        ));

        // Get the Instance IDs
        $instances = $result->get('Instances');
        $instance = $instances[0];
        $instance_id = $instance['InstanceId'];

        // Save the Instance ID to the Database
        $this->launcher->instance_id = $instance_id;
        $this->launcher->save();

        // Wait until the instance is launched
        $ec2Client->getWaiter('InstanceStatusOk',array(
            'InstanceIds' => [$instance_id],
        ));

        // We wait an additional 5 seconds,
        // to make sure an IP Address is assigned
        sleep(5);

        // Describe the now-running instance to get the public ip address
        $result = $ec2Client->describeInstances(array(
            'InstanceIds' => [$instance_id],
        ));

        // Store the IP Address in the Database
        $this->launcher->ip_address = $result['Reservations'][0]['Instances'][0]['PublicIpAddress'];
        $this->launcher->private_ip_address = $result['Reservations'][0]['Instances'][0]['NetworkInterfaces'][0]['PrivateIpAddress'];
        $this->launcher->status = 'Instance Launched';
        $this->launcher->save();


    }
}
