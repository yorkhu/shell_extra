#! /bin/sh

echo $HOME

p=$(pwd)
ln -s $p/.bash_profile $HOME
ln -s $p/.bashrc $HOME
ln -s $p/.profile $HOME

ln -s $p/dbsync.php /usr/local/bin/dbsync
ln -s $p/mkproject.php /usr/local/bin/mkproject


