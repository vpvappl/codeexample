### 1 -------------------------------------------------------------
#!/bin/bash
wget http://www.litres.ru/static/ds/partners_utf.yml.gz&&
var_dir=`pwd`'/'
var_path_file=$var_dir'partners_utf.yml.gz'
gunzip -f $var_path_file

### 2 -------------------------------------------------------------

#!/bin/sh

rm -rf /home/admin/web/site.ru/public_html/frontend/web/cache/*&&
rm -rf /home/admin/web/site.ru/public_html/frontend/runtime/debug/*&&

cd /home/admin/web/site.ru/public_html&&

/usr/bin/mysqldump --opt -uadmin_fast -padmin_fast -hlocalhost admin_fast > /home/admin/web/backup.site.ru/public_html/bistro$
/usr/bin/zip -r /home/admin/web/backup.site.ru/public_html/site_backup.zip .*&&

MESSAGE="Today, `date '+%d.%m.%Y'`, in `date '+%H:%M:%S'` was created the site archive == http://backup.site.ru/site_b$

echo "$MESSAGE" | mail -s "BISTROKNIGA-151-archive" "site@yandex.ru"
