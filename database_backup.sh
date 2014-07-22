#!/bin/sh

project_name='mam'
project_path='/usr/home/mam/home.steelemotion.com'
db_name='mam_www'
db_user='mam'
db_password='vNovom30Vete='

current_date=`date +%Y%m%d.%H%M`
current_month=`date +%Y-%m`

dump_name=${current_date}.${db_name}

mkdir ${project_path}/_dumps/${current_month}

echo "DB dump..."
/usr/local/bin/mysqldump --host=localhost --add-drop-table --complete-insert --quote-names --routines --password=${db_password} --user=${db_user} ${db_name} > ${project_path}/${dump_name}.sql
/usr/local/bin/zip ${project_path}/_dumps/${current_month}/${dump_name}.sql.zip ${dump_name}.sql
rm ${project_path}/${dump_name}.sql