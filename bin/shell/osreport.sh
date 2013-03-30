#!/bin/sh

### A script designed to be of use to consultants for audits and to support people for troubleshooting.
### It scans basic configuration of the OS and of the AMP stack
###
### Tested so far on Debian 5 and RHEL 5, Centos 5, Ubuntu 10.04
### Does not support Oracle nor Postgresql dbs yet. Only supported webserver is Apache.
### Look at the bottom of the script and at all @todo tags for ideas for improvement.

### notes for wannabe shell scripters:
### 1. be portable! https://wiki.ubuntu.com/DashAsBinSh - run checkbashisms on this
### 2. tail+2 | sort to sort avoiding first line

echo "### eZ Basic System Analysis script ###"
echo "    Version: 20120215"
echo "    Did you take a look at pt-summary from percona-toolkit? It is a valid alternative"


usage()
{
    cat << EOF
USAGE: $0 [options]
  This script dumps a lot of information about the server, which is usually useful for auditing eZ Publish installations.
  It also creates (in the current directory) tarballs of apache, mysql, php and varnish configurations.
OPTIONS:
   -h      Show this message
   -v      Verbose: dump complete list of executing processes, output of netstat command and list of apache log files
   -z      Skip creating zip files of configurations
   -s $srv Server hostname (for ftp upload of confs. Not done when -z is used)
   -u $usr Username        (same)
   -p $pwd Password        (same)
EOF
}

VERBOSE=0
SKIPZIP=0
SERVER=
USER=
PASSWORD=
while getopts ":zs:u:p:vh" OPTION
do
    case $OPTION in
        s)
            SERVER=$OPTARG
            ;;
        u)
            USER=$OPTARG
            ;;
        p)
            PASSWORD=$OPTARG
            ;;
        v)
            VERBOSE=1
            ;;
        z)
            SKIPZIP=1
            ;;
        h)
            usage
            exit 1
            ;;
        ?)
            usage
            exit
            ;;
     esac
done

#if [ "$1" = "--help" ]; then
#  echo Usage: $0 [host username password]
#  echo        optional arguments are for uploading confs to remote ftp server
#  # nb: if this script is copied to /tmp, mounted noexec, it cannot be run as ./analysis.sh,
#  # but only as . ./analysis.sh. In that case the 'exit 0' call will end the terminal session
#  exit 0
#fi

echo "### Start Time:" `date`

printf "\n### Uptime\n"
uptime


printf "\n### Linux kernel version\n"
uname -a

### @todo check lsb-release first if it is installed
if [ -f /etc/issue ]; then
  printf "\n### OS\n"
  cat /etc/issue
fi

OS=unknown
if [ -f /etc/redhat-release ]; then
  printf "\n### OS version\n"
  cat /etc/redhat-release
  OS=redhat
else
  if [ -f /etc/debian_version ]; then
    printf "\n### OS version\n"
    cat /etc/debian_version
    OS=debian
  fi
fi


printf "\n### Memory\n"
cat /proc/meminfo
echo
free -t -m


# another nice way to count processors (as opposed to cores): numactl --hardware
printf "\n### Processors\n"
cat /proc/cpuinfo


printf "\n### Disks\n"
df -h
echo
cat /etc/mtab
# @todo
# hdparm -tT /dev/hda
# tune2fs -l
# dmesg | grep ext3
# dmesg | grep sda


printf "\n### Network interfaces\n"
/sbin/ifconfig
# @todo
# mii-tool -v eth0
# ethtool eth0

if [ $VERBOSE -eq 1 ]; then
    printf "\n### Netstat\n"
    netstat -pante 2>/dev/null
    # more sorting?
    #netstat -pante  2>/dev/null | more +3 | sort -k4
fi

printf "\n### Processes\n"

printf "\n### Top 10 processes by RAM\n"
ps -A --sort -rss -o comm,pmem | head -n 11

printf "\n### Services\n"
if [ "$OS" = "redhat" ]; then
  /sbin/chkconfig --list | grep \:on
else
  # @todo this might be a little simplistic...
  ls /etc/rc3.d/ | grep ^S
fi

if [ $VERBOSE -eq 1 ]; then
    printf "\n### All processes\n"
    ps aux --sort cmd | fgrep -v \[  | fgrep -v "ps aux"
fi

printf "\n.\n"
top -n 1

# 1 minute of running stats
#vmstat 3 20

printf "\n### IO stat\n"
iostat -d -k -n


printf "\n### Inside a VM?\n"
GUEST=`ps -ef | grep vmware-guestd | grep -v grep`
if [ -n "$GUEST" ]; then
  echo Definitely VMWare
else
  GUEST=`ps -ef | grep xenwatch | grep -v grep`
  if [ -n "$GUEST" ]; then
    echo Definitely Xen
  else
    echo Maybe not
  fi
fi


### @todo PHP information
### launch eg. php -v to get version number - but we need to find out the correct version of php in use...


### @todo support also nginx, lighttpd

APACHE_PID=
for APACHE in apache2 httpd apache
do
  APACHE_PID=$(ps ax -o user,pid,command | grep $APACHE | grep -v grep | grep ^root | awk '{print $2}')
  if [ -n "$APACHE_PID" ]; then
    break
  fi
done;
# variable apache pid: /var/run/apache2.pid, /var/run/apache.pid, /var/run/httpd.pid
#APACHE_PID=`cat /var/run/apache2.pid`

### @todo add support for many apache root processes found

if [ -n "$APACHE_PID" ]; then
  printf "\n### Apache processes (parent pid %s)\n" $APACHE_PID
  APACHE_PROCS=$(ps --ppid $APACHE_PID | grep $APACHE | grep -v grep | wc -l)
  echo $APACHE_PROCS found

  APACHE_RAM=$(ps ux --ppid=$APACHE_PID | awk '// { x += $6 } END { print x  }')
  APACHE_AVGRAM=`expr $APACHE_RAM / $APACHE_PROCS`
  echo "Total memory (for child processes): $APACHE_RAM, average: $APACHE_AVGRAM"

  echo 'Parent started: '`ps -o lstart --no-headers --pid $APACHE_PID`
  ps -o pid,cmd,lstart,etime,rss,pcpu,pmem --ppid $APACHE_PID
fi

if [ $VERBOSE -eq 1 ]; then
    APACHE_LOGS=
    for APACHE in apache2 httpd apache
    do
      if [ -d "/var/log/$APACHE" ]; then
        APACHE_LOGS="/var/log/$APACHE"
        break
      fi
    done;
    if [ -n "$APACHE_LOGS" ]; then
      # @todo tail apache error logs
      printf "\n### Apache logs\n"
      ls -ltr $APACHE_LOGS
      echo 'Total size: '`du -sh $APACHE_LOGS`
    fi
fi

# @todo recover apache version (eg from response headers of http://localhost)


MYSQL_PID=$(ps aux | grep mysqld | grep -v  mysqld_safe | grep -v logger | grep -v grep | awk '{print $2}')
if [ -n "$MYSQL_PID" ]; then
  printf "\n### MySql process (%s)" $MYSQL_PID
  echo 'KB: '`ps -o rss --no-headers --pid $MYSQL_PID`
  echo 'Started: '`ps -o lstart --no-headers --pid $MYSQL_PID`
  ### @todo total mysql data size
fi


SOLR_PID=$(ps aux | grep start.jar | grep -v grep | grep -v " /bash" | grep -v " /bin/bash" | grep -v " /sh" | awk '{print $2}')
if [ -n "$SOLR_PID" ]; then
  printf "\n### SOLR process (%s)\n" $SOLR_PID
  echo 'KB: '`ps -o rss --no-headers --pid $SOLR_PID`
  echo 'Started: '`ps -o lstart --no-headers --pid $SOLR_PID`
  JAVA_CMD=$(ps aux | grep start.jar | grep -v grep | grep -v " /bash" | grep -v " /bin/bash" | grep -v " /sh" | awk '{print $11}')
  $JAVA_CMD -version
  ### @todo test if port is really 8983
  ### @todo test if wget is present
  echo 'Objects: '
  wget -q -O - "$@" http://localhost:8983/solr/admin/stats.jsp | grep -A 1 '<stat name="numDocs" >'
fi


VARNISH_PID=$(ps aux | grep varnishd | grep -v grep | grep ^root | awk '{print $2}')
if [ -n "$VARNISH_PID" ]; then
  printf "\n### VARNISH process (%s)" $VARNISH_PID
  #echo 'KB: '`ps -o rss --no-headers --pid $VARNISH_PID`
  ps ux --ppid=$VARNISH_PID | awk '// { x += $6 } END { print "Total memory (for child processes) KB: " x  }'
  echo 'Started: '`ps -o lstart --no-headers --pid $VARNISH_PID`
fi


DATE=`date +%Y%m%d_%H%M%S`

if [ $SKIPZIP -ne 1 ]; then

# @todo add support for getting symlinks (-h) - but avoid recursion
# @todo support Apache installed in /usr/local/apache or elsewhere
#       see: http://wiki.apache.org/httpd/DistrosDefaultLayout
if [ -d /etc/$APACHE ]; then
    printf "\n### Zipping Apache conf\n"
    tar cvf apache_conf_$DATE.tar /etc/$APACHE/ --exclude /etc/$APACHE/logs --exclude /etc/$APACHE/modules  --exclude /etc/$APACHE/run
    bzip2 apache_conf_$DATE.tar
#else
#    printf "Apache conf not found\n"
fi

# instead of echoing php info here, just zip up the whole dirs just like we do with Apache
# @todo add support for getting symlinks (-h) - but avoid recursion
# @todo support PHP installed in /usr/local or elsewhere
if [ -d /etc/php5 -o -d /etc/php.d -o -d /etc/php-zts.d -o -f /etc/php.ini ]; then
    printf "\n### Zipping Php conf\n"
    tar cvf php5_conf_$DATE.tar /etc/php5 /etc/php.d /etc/php-zts.d /etc/php.ini 2>/dev/null
    bzip2 php5_conf_$DATE.tar
fi

# @todo add support for getting symlinks (-h) - but avoid recursion
# @todo support MySql installed in /usr/local or elsewhere
if [ -f '/etc/my.cnf' -o -d '/etc/mysql' ]; then
    printf "\n### Zipping Mysql conf\n"
    tar cvf mysql_conf_$DATE.tar /etc/my.cnf /etc/mysql 2>/dev/null
    bzip2 mysql_conf_$DATE.tar
fi

if [ -d '/etc/varnish' ]; then
    printf "\n### Zipping Varnish conf\n"
    tar cvf varnish_conf_$DATE.tar /etc/varnish --exclude /etc/varnish/secret
    bzip2 varnish_conf_$DATE.tar
fi


# @todo upload the results (so far) of this script too

# upload confs to remote ftp server
if [ -n "$SERVER" -a -n "$USER" -a -n "$PASSWORD" ]; then
  printf "\n### Uploading files via ftp to server %s" $SERVER
  ftp -n $SERVER 2>/dev/null <<END_SCRIPT
quote USER $USER
quote PASS $PASSWORD
bin
put apache_conf_$DATE.tar.bz2
put php_conf_$DATE.tar.bz2
put php5_conf_$DATE.tar.bz2
put mysql_conf_$DATE.tar.bz2
put varnish_conf_$DATE.tar.bz2
quit
END_SCRIPT
fi

#end of skipzips
fi

### @todo

### MORE TESTS
### patch status: yum, apt-get
### crontabs
### mysql: find username+pwd from ezp conf, download mysqltuner.pl and run it against db
#wget http://mysqltuner.pl/mysqltuner.pl
#perl ./mysqltuner.pl --host --port --user --password --assume-memory...
#rm -y mysqltuner.pl
### mysql: storage engine & charset per table: SELECT TABLE_NAME, TABLE_COLLATION, ENGINE, TABLE_ROWS FROM information_schema.TABLES where TABLE_SCHEMA = 'xxx';
### mysql: full config: show variables\G
### mysql: show global status;

### eZP tests
### filesystem perms on eZP directory
### size / rotation frequence of logs
### count data in tables: ezsession, ezuser, ezworkflow_process, ezpending_actions, ezsearch_word
