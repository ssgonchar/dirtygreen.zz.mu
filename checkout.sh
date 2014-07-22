###checkout for home.steelemotion.com###
#!/bin/sh
project_name='home.steelemotion'
project_path='/usr/home/mam/home.steelemotion.com'
db_user='mam'
db_name='mam_www'
db_password='vNovom30Vete='

current_date=`date +%Y%m%d.%H%M`
echo ${current_date}

#echo "DB dump..."
#mysqldump --host=localhost --add-drop-table --complete-insert --quote-names --routines --password=${db_password} --user=${db_user} ${db_name} > ${db_name}.sql
#zip ${current_date}.${db_name}.sql.zip ${db_name}.sql
#rm ${db_name}.sql


echo "SVN checkout to DIR www1..."
svn export http://svn.kvadrosoft.com/mam www1

echo "replace config..."
mv www1/.htaccess-server www1/.htaccess


echo "Rename DIR www to www-currdate..."
mv www www-${current_date}

echo "Create DIR www..."
mkdir www

echo "Moving application from DIR www1 to DIR www..."
mv www1/* www/
cp www1/.htaccess www/

echo "Removing DIR www1..."
rm -r -f www1

#echo "Moving DIRs images..."
#mv www-${current_date}/images/* www/images/
#chmod -R 0777 www/images

echo "Add application to zip-archive (DIR curdate-www) and remove source dir ..."
zip -r -m --symlinks ${current_date}.${project_name}.application.zip www-${current_date}

#echo "Linking DIRs attachments..."
#ln -s /usr/local/www/eippi.kvadrosoft.com/settings/attachments /usr/local/www/eippi.kvadrosoft.com/www/img/att

echo "Enjoy!"
