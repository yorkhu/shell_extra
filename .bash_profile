# Exports.
export PATH="/usr/local/bin:/usr/local/sbin:$PATH:/Applications/MAMP/Library/bin"
export TERM=xterm-256color
export CLICOLOR=true
# Nice colors in terminal
export LSCOLORS=ExFxCxDxBxegedabagacad

# Bash colors.
export color_none='\e[0m'
export color_white='\e[1;37m'
export color_black='\e[0;30m'
export color_blue='\e[0;34m'
export color_light_blue='\e[1;34m'
export color_green='\e[0;32m'
export color_light_green='\033[32;1m'
export color_cyan='\e[0;36m'
export color_light_cyan='\e[1;36m'
export color_red="\e[0;31m"
export color_light_red='\e[1;31m'
export color_purple='\e[0;35m'
export color_light_purple='\e[1;35m'
export color_brown='\e[0;33m'
export color_yellow='\e[1;33m'
export color_gray='\e[0;90m'
export color_light_gray='\e[0;37m'

# Include aliases.
source $HOME/.shell_extra/.aliases
source $HOME/.shell_extra/.aliases_project

## Configure the bash prompt.

# Date piece. It's not used at the moment, but let's keep it here anyway. It could be useful.
DATE_PIECE="\[${color_gray}\]\$(date '+%H:%M:%S')\[${color_none}\]"

# Path piece.
PATH_PIECE="\w"

# Git piece.
if [ -f /usr/local/etc/bash_completion.d/git-completion.bash ]; then
  . /usr/local/etc/bash_completion.d/git-completion.bash
  . /usr/local/etc/bash_completion.d/git-prompt.sh

  GIT_PS1_SHOWDIRTYSTATE=true
  GIT_PIECE='$(__git_ps1)'
else
  GIT_PIECE=''
fi

_dir_chomp () {
  # source:
  # http://stackoverflow.com/questions/3497885/code-challenge-bash-prompt-path-shortener
  # http://superuser.com/questions/271212/is-it-possible-to-get-bash-to-display-the-first-letter-of-each-directory-in-my-w

  local sd='~/projects'
  local p=${1/#$HOME/\~} b s

  if [ $sd != ${p:0:${#sd}} ]; then
    echo $p;
    return;
  fi

  s=${#p}
  while [[ $p != ${p//\/} ]]&&(($s>$2))
  do
    p=${p#/}
    [[ $p =~ \.?. ]]
    b=$b/${BASH_REMATCH[0]}
    p=${p#*/}
    ((s=${#b}+${#p}))
    s=${#p}
  done
  echo ${b/\/~/\~}${b+/}$p
}

# Bash prompt.
#export PS1="${DATE_PIECE} \[${color_light_green}\]\u@\h\[${color_none}\]:\[${color_light_blue}\]${PATH_PIECE}${color_light_red}${GIT_PIECE}\[${color_none}\]\\$\[${color_none}\] "
#export PS1="\[${color_light_green}\]\u@\h\[${color_none}\]:\[${color_light_blue}\]${PATH_PIECE}${color_light_red}${GIT_PIECE}\[${color_none}\]\\$\[${color_none}\] "

export PS1="\[${color_light_green}\]\u@\h\[${color_none}\]:\[${color_light_blue}\]"'$(
  _dir_chomp "$(pwd)" 32
)'"${color_light_red}${GIT_PIECE}\[${color_none}\]\\$\[${color_none}\] "



# Hello Messsage
if [ $USER != "root" ]; then
  if [ -f ~/.shell_extra/ascii_druplicon_blank.txt ]; then
    # source: http://deekayen.net/druplicon_motd
    cat ~/.shell_extra/ascii_druplicon_blank.txt
  fi
  echo -ne "Uptime: "; uptime
fi

if [ -f /opt/local/etc/bash_completion ]; then
   . /opt/local/etc/bash_completion
fi

if [ $USER != "root" ]; then
  PATH=$PATH:$HOME/.rvm/bin # Add RVM to PATH for scripting
  [[ -s "$HOME/.rvm/scripts/rvm" ]] && source "$HOME/.rvm/scripts/rvm"
  # Load RVM into a shell session *as a function*
fi
