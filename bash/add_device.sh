#!/bin/bash
# Author: Rudie Shahinian

usage()
{
echo "Usage: $0 -h hostname -d domain -i ip-address/mask -p password -f framed-route1/mask,framed-route2/mask -m mac-address"
echo ""
}

options=0
while getopts "d:f:h:i:p:m:s:" opt; do
  case $opt in
    d)
      domain=${OPTARG}
      options=$[options+1]
      ;;
    f)
      framed_routes="`echo ${OPTARG} | sed 's/,/ /g'`"
      ;;
    h)
      hostname=${OPTARG}
      options=$[options+1]
      ;;
    i)
      ip=${OPTARG}
      if [ "`echo ${ip} | grep \/`" != "" ]; then
        mask="`ipcalc -m ${ip} |  awk -F '=' '{ print $2 }'`"
        ip="`echo ${ip} | awk -F '/' '{ print $1 }'`"
      else
        mask="255.255.255.255"
      fi
      ;;
    p)
      password=${OPTARG}
      options=$[options+1]
      ;;
    m)
      mac_address=${OPTARG}
      ;;
    s)
      serial=${OPTARG}
      ;;
    \?)
      echo "Invalid option: -${OPTARG}" >&2
      exit 1
      ;;
    :)
      echo "Option -${OPTARG} requires an argument." >&2
      exit 1
      ;;
  esac
done
if [ ${options} -lt 3 ]; then
  echo ""
  echo "You need to specify hostname, domain, IP address and password"
  usage
  exit 1
fi

fqdn="${hostname}.${domain}"
tmp_ldif="/tmp/radius_ldif.tmp"

# If IP address is not specified we need to add force parameter
if [ -n "${ip}" ]; then
    /usr/bin/ipa host-add ${fqdn} --ip-address=${ip} --os=DeviceOS --password=${password}
else
    /usr/bin/ipa host-add ${fqdn} --os=DeviceOS --password=${password} --force
fi


echo "dn: fqdn=${fqdn},cn=computers,cn=accounts,dc=lab,dc=company,dc=net" > ${tmp_ldif}
echo "changetype: modify" >> ${tmp_ldif}
echo "add: objectClass" >> ${tmp_ldif}
echo "objectClass: radiusprofile" >> ${tmp_ldif}
echo "-" >> ${tmp_ldif}
echo "add: radiusFramedCompression" >> ${tmp_ldif}
echo "radiusFramedCompression: None" >> ${tmp_ldif}
echo "-" >> ${tmp_ldif}
if [ -n "${ip}" ]; then
    echo "add: radiusFramedIPAddress" >> ${tmp_ldif}
    echo "radiusFramedIPAddress: ${ip}" >> ${tmp_ldif}a
    echo "-" >> ${tmp_ldif}
    echo "add: radiusFramedIPNetmask" >> ${tmp_ldif}
    echo "radiusFramedIPNetmask: ${mask}" >> ${tmp_ldif}
    echo "-" >> ${tmp_ldif}
fi
echo "add: radiusFramedProtocol" >> ${tmp_ldif}
echo "radiusFramedProtocol: PPP" >> ${tmp_ldif}
echo "-" >> ${tmp_ldif}
echo "add: radiusFramedMTU" >> ${tmp_ldif}
echo "radiusFramedMTU: 1500" >> ${tmp_ldif}
if [ -n "${serial}" ]; then
    echo "-" >> ${tmp_ldif}
    echo "add: serialNumber" >> ${tmp_ldif}
    echo "serialNumber: ${serial}" >>  ${tmp_ldif}
fi

for network in ${framed_routes}; do
    echo "-" >> ${tmp_ldif}
    echo "add: radiusFramedRoute" >> ${tmp_ldif}
    echo "radiusFramedRoute: ${network} ${ip} 1" >> ${tmp_ldif}
done

if [ -n "${mac_address}" ]; then
    echo "-" >> ${tmp_ldif}
    echo "add: macAddress" >> ${tmp_ldif}
    echo "macAddress: ${mac_address}" >> ${tmp_ldif}
fi

ldapmodify -H ldap://ldap.company.net -Y GSSAPI -f ${tmp_ldif}
rm -f ${tmp_ldif}
