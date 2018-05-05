from fabric.api import *
from fabric.colors import *

# Usage
# $ fab prod deploy

def prod():
    env.hosts = [ "ternel@ternel.net" ]
    env.srvdir = '/home/ternel/www/weekend/'
    env.git_branch = 'master'

def deploy():
    with cd('%s' % env.srvdir):
        green("Deploy and install vendors")
        run('git checkout %s' % env.git_branch)
        run('git pull')
        run('php /home/ternel/www/weekend/shared/composer.phar install --no-dev -o')
