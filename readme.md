# Padento 2.0

### Create symbolic link to storage folder

ln -s /home/vagrant/code/freelancing/padentoneu/storage/app/public /home/vagrant/code/freelancing/padentoneu/public/storage

### Change php version on server

cd /www/htdocs/w0149da0
mkdir bin
ln -s /usr/bin/php70 /www/htdocs/w0149da0/bin/php
PATH="/www/htdocs/w0149da0/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games"
vi ~/.bashrc

adjust PATH variable as below in .bashrc
PATH="/www/htdocs/w0149da0/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games"