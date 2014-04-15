#!/usr/bin/perl -w
#
# Author: Rudie Shahinian
# Version: 2.1

use strict;
use warnings;
use Getopt::Long;
use FindBin;
use lib "$FindBin::Bin/../";
use VMware::VIRuntime;
use JSON;
use AppUtil::HostUtil;
$Util::script_version = "2.1";
sub discover;
Opts::parse();

my @json_arr;
my %json_h;
my %discovered_hosts;
my %discovered_clusters;
my %discovered_folders;
my %discovered_vms;
my $id;

Util::connect();
@json_arr = discover();
$json_h{'datacenters'} = \@json_arr;
print encode_json \%json_h;
Util::disconnect();

sub discover() {
	my @all_datacenters_arr;
	my $level = 0;
	my $datacenter_views = Vim::find_entity_views (view_type => 'Datacenter');
	foreach(@$datacenter_views) {
        #Util::trace(0,"\n***************Datacenter $name***************\n");
		my %datacenter_h;
		my @datacenter_arr = discover_datacenter(datacenter => $_,level=>$level+1);
        if(scalar(@datacenter_arr) > 0){
			$datacenter_h{'name'}=$_->name;
			$datacenter_h{'id'}=$id++;
			$datacenter_h{'clusters'}=\@datacenter_arr;
			push(@all_datacenters_arr,\%datacenter_h );
        }
	}
    return @all_datacenters_arr;
     
}

sub discover_datacenter() {
	my %args = @_;
	my $datacenter = $args{datacenter};
	my $level = $args{level};
	my @datacenter_arr;
  
	my $result = get_entities(view_type => 'ClusterComputeResource', obj => $datacenter);
   	display(name=>"DataCenter :",value=>$datacenter->name,level=>$level);
	foreach (@$result) {
		my $obj_content = $_;
		my $mob = $obj_content->obj;
		my $obj = Vim::get_view(mo_ref=>$mob);
		my @cluster_arr = discover_cluster(cluster => $obj,level=>$level+1);
#		print Dumper(@cluster_arr);
		if(scalar(@cluster_arr) > 0){
			my %cluster_h;
			$cluster_h{'name'} = $obj->name;
			$cluster_h{'id'}=$id++;
			$cluster_h{'hosts'} = \@cluster_arr;
			push(@datacenter_arr,\%cluster_h );
		}
	}
	return @datacenter_arr;
	
}

sub discover_cluster() {
	my %args = @_;
	my $cluster = $args{cluster};
	my $level = $args{level};
	my $result = get_entities(view_type => 'HostSystem', obj => $cluster);
	my @host_arr;
    display(name=>"Cluster :",value=>$cluster->name,level=>$level);
	foreach (@$result) {
		my $obj_content = $_;
		my $mob = $obj_content->obj;
		my $obj = Vim::get_view(mo_ref=>$mob);
		if (!exists($discovered_hosts{$obj->name})) {
			my @vms_arr = discover_host(host => $obj,level=>$level+1);
			if(scalar(@vms_arr) > 0){
				my %host_h;
				my $i = 0;
				my $storage_capacity = 0;
				my $storage_usage = 0;
				while(exists $obj->config->fileSystemVolume->mountInfo->[$i]){
					$storage_capacity += $obj->config->fileSystemVolume->mountInfo->[$i]->volume->capacity;
					$i++;
				}
				$host_h{'hostname'} = $obj->config->network->dnsConfig->hostName . '.'. $obj->config->network->dnsConfig->domainName;
				$host_h{'name'} = $obj->name;
				$host_h{'cpu_model'} = $obj->summary->hardware->cpuModel;
      			$host_h{'cpu_usage_mhz'} = $obj->summary->quickStats->overallCpuUsage ;
      			$host_h{'cpu_capacity_mhz'} = ($obj->summary->hardware->cpuMhz * $obj->summary->hardware->numCpuCores) ;
      			$host_h{'cpu_usage_percent'} = sprintf('%.2f',($obj->summary->quickStats->overallCpuUsage / ($obj->summary->hardware->cpuMhz * $obj->summary->hardware->numCpuCores))*100);
				$host_h{'num_cpu'} = $obj->summary->hardware->numCpuCores;
				$host_h{'num_nic'} = $obj->summary->hardware->numNics;
				$host_h{'num_vms'} = scalar(@vms_arr);
				$host_h{'storage_capacity_gb'} = sprintf('%.2f',$obj->summary->hardware->memorySize/(1024*1024*1024));
				$host_h{'mem_usage_mb'} = $obj->summary->quickStats->overallMemoryUsage;
				$host_h{'mem_capacity_mb'} = sprintf('%.2f',$obj->summary->hardware->memorySize/(1024*1024));
				$host_h{'mem_usage_percent'} = sprintf('%.2f',($obj->summary->quickStats->overallMemoryUsage/($obj->summary->hardware->memorySize/(1024*1024))*100));
				$host_h{'ip_addresses'} = $obj->config->network->dnsConfig->address;
				$host_h{'uptime'} = int($obj->summary->quickStats->uptime/86400 );
				$host_h{'connection_state'} = $obj->runtime->connectionState->val;
				$host_h{'id'}=$id++;
				$host_h{'vms'} = \@vms_arr;
				push(@host_arr,\%host_h );
			}
		}
	}
	return @host_arr;

}

sub discover_host() {
   my %args = @_;
   my $host = $args{host};
   my $level = $args{level};
   my $result = get_entities(view_type => 'VirtualMachine', obj => $host);
   my @vm_arr;
   display(name=>"Host :",value=>$host->name,level=>$level);                         
   foreach (@$result) {
      my $obj_content = $_;
      my $mob = $obj_content->obj;
      my $obj = Vim::get_view(mo_ref=>$mob);
      my $pobj = Vim::get_view(mo_ref=>$obj->parent);
      my $power_state = $obj->summary->runtime->powerState->val;
      display(name=>"VM :",value=>$obj->name,level=>$level);
      my %vm_h;
      $vm_h{'name'}=$obj->name;

      if(substr($obj->name, 0, 2) =~ /cc/i){
	      $vm_h{'type'}='concentrator';
      }
      elsif(substr($obj->name, 0, 2) =~ /rs/i){
	      $vm_h{'type'}='routeserver';
      }
      #elsif(substr($obj->name, 0, 2) =~ /gw/i){
	  #    $vm_h{'type'}='gateway';
      #}
      else {
          $vm_h{'type'}='guest';
      }
      $vm_h{'powerstate'}=$power_state;
      
      $vm_h{'ip_address'} = $obj->guest->ipAddress;
      if(defined($obj->guest->net)){
	      $vm_h{'ip_addresses'} = $obj->guest->net->[0]->ipAddress;
      }
      else {
		$vm_h{'ip_addresses'} = ();
      }
      $vm_h{'hostname'} = $obj->guest->hostName;
      
      $vm_h{'cpu_usage_mhz'} = $obj->summary->quickStats->overallCpuDemand;
      $vm_h{'cpu_capacity_mhz'} = $obj->config->cpuAllocation->shares->shares;
      $vm_h{'cpu_usage_percent'} = sprintf('%.2f',($obj->summary->quickStats->overallCpuDemand/$obj->config->cpuAllocation->shares->shares)*100);
      $vm_h{'num_cpu'} = $obj->config->hardware->numCPU;
      $vm_h{'uptime'} = int($obj->summary->quickStats->uptimeSeconds/86400 );
      $vm_h{'mem_usage_mb'} = $obj->summary->quickStats->guestMemoryUsage;
      if(defined($obj->summary->runtime->maxMemoryUsage)){
		  $vm_h{'mem_capacity_mb'} = $obj->summary->runtime->maxMemoryUsage;
		  $vm_h{'mem_usage_percent'} = sprintf('%.2f',($obj->summary->quickStats->guestMemoryUsage/$obj->summary->runtime->maxMemoryUsage)*100);
      }
      else {
		  $vm_h{'mem_capacity_mb'} = ();
		  $vm_h{'mem_usage_percent'} = ();
      }
      if(defined($obj->storage->perDatastoreUsage)){
		  $vm_h{'storage_usage_gb'} = sprintf('%.2f',$obj->storage->perDatastoreUsage->[0]->committed/(1024*1024));
		  $vm_h{'storage_capacity_gb'} = sprintf('%.2f',$obj->storage->perDatastoreUsage->[0]->uncommitted/(1024*1024));
		  $vm_h{'storage_usage_percent'} = sprintf('%.2f',($obj->storage->perDatastoreUsage->[0]->committed/($obj->storage->perDatastoreUsage->[0]->uncommitted + $obj->storage->perDatastoreUsage->[0]->committed))*100);
      }
      else {
		  $vm_h{'storage_usage_gb'} = ();
		  $vm_h{'storage_capacity_gb'} = ();
		  $vm_h{'storage_usage_percent'} = ();
      }
      $vm_h{'status'} = $obj->overallStatus->val;
      $vm_h{'id'}=$id++;
      push(@vm_arr, \%vm_h);
   }
	return @vm_arr;
}

sub get_entities {
   my %args = @_;
   my $view_type = $args{view_type};
   my $obj = $args{obj};
   
   my $sc = Vim::get_service_content();
   my $service = Vim::get_vim_service();

   my $property_spec = PropertySpec->new(all => 0,
                                         type => $view_type->get_backing_type());
   my $property_filter_spec = $view_type->get_search_filter_spec($obj,[$property_spec]);
   my $obj_contents = $service->RetrieveProperties(_this => $sc->propertyCollector,
                                                   specSet => $property_filter_spec);
   my $result = Util::check_fault($obj_contents);
   return $result;
}

sub display {
   my %args = @_;
   my $name = $args{name};
   my $value = $args{value};
   my $level = $args{level};
}

sub get_search_filter_spec {
   my ($class, $moref, $property_spec) = @_;
   my $resourcePoolTraversalSpec =
      TraversalSpec->new(name => 'resourcePoolTraversalSpec',
                         type => 'ResourcePool',
                         path => 'resourcePool',
                         skip => 1,
                         selectSet => [SelectionSpec->new(name => 'resourcePoolTraversalSpec'),
                           SelectionSpec->new(name => 'resourcePoolVmTraversalSpec'),]);

   my $resourcePoolVmTraversalSpec =
      TraversalSpec->new(name => 'resourcePoolVmTraversalSpec',
                         type => 'ResourcePool',
                         path => 'vm',
                         skip => 1);

   my $computeResourceRpTraversalSpec =
      TraversalSpec->new(name => 'computeResourceRpTraversalSpec',
                type => 'ComputeResource',
                path => 'resourcePool',
                skip => 1,
                selectSet => [SelectionSpec->new(name => 'resourcePoolTraversalSpec')]);


   my $computeResourceHostTraversalSpec =
      TraversalSpec->new(name => 'computeResourceHostTraversalSpec',
                         type => 'ComputeResource',
                         path => 'host',
                         skip => 1);

   my $datacenterHostTraversalSpec =
      TraversalSpec->new(name => 'datacenterHostTraversalSpec',
                     type => 'Datacenter',
                     path => 'hostFolder',
                     skip => 1,
                     selectSet => [SelectionSpec->new(name => "folderTraversalSpec")]);

   my $datacenterVmTraversalSpec =
      TraversalSpec->new(name => 'datacenterVmTraversalSpec',
                     type => 'Datacenter',
                     path => 'vmFolder',
                     skip => 1,
                     selectSet => [SelectionSpec->new(name => "folderTraversalSpec")]);

   my $hostVmTraversalSpec =
      TraversalSpec->new(name => 'hostVmTraversalSpec',
                     type => 'HostSystem',
                     path => 'vm',
                     skip => 1,
                     selectSet => [SelectionSpec->new(name => "folderTraversalSpec")]);

   my $folderTraversalSpec =
      TraversalSpec->new(name => 'folderTraversalSpec',
                       type => 'Folder',
                       path => 'childEntity',
                       skip => 1,
                       selectSet => [SelectionSpec->new(name => 'folderTraversalSpec'),
                       SelectionSpec->new(name => 'datacenterHostTraversalSpec'),
                       SelectionSpec->new(name => 'datacenterVmTraversalSpec',),
                       SelectionSpec->new(name => 'computeResourceRpTraversalSpec'),
                       SelectionSpec->new(name => 'computeResourceHostTraversalSpec'),
                       SelectionSpec->new(name => 'hostVmTraversalSpec'),
                       SelectionSpec->new(name => 'resourcePoolVmTraversalSpec'),
                       ]);

   my $obj_spec = ObjectSpec->new(obj => $moref,
                                  skip => 1,
                                  selectSet => [$folderTraversalSpec,
                                                $datacenterVmTraversalSpec,
                                                $datacenterHostTraversalSpec,
                                                ]);

   return PropertyFilterSpec->new(propSet => $property_spec,
                                  objectSet => [$obj_spec]);
}




