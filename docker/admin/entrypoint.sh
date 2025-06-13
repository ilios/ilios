#!/bin/bash
set -m -euf -o pipefail

/bin/echo "Entrypoint ssh-admin container"

if [[ $GITHUB_ACCOUNT_SSH_USERS ]]; then
	# keep a copy of the default file separator
	ORIGINAL_IFS=$IFS

	# Users are separated with semi-colons
	IFS=';'
	for user in $GITHUB_ACCOUNT_SSH_USERS
	do
			/bin/echo "Creating account for user ${user}"
			SSH_DIR="/home/$user/.ssh"
			/usr/sbin/useradd -ms /bin/bash -G sudo $user
			/bin/mkdir $SSH_DIR
			/usr/bin/wget --quiet -O - "https://github.com/${user}.keys" >> "${SSH_DIR}/authorized_keys"
			/bin/chown -R "${user}:${user}" $SSH_DIR
			/bin/chmod 700 $SSH_DIR
			/bin/chmod 600 "${SSH_DIR}/authorized_keys"
	done

	IFS=$ORIGINAL_IFS
fi

# set the PS1 prompt and colorization
cat << EOF > /etc/profile.d/color_prompt.sh 
export TERM=xterm-256color
# set the CLI color
export CLICOLOR=1
# set colors for the 'ls' listing
export LSCOLORS=gafacadabaegedabagacad
export PS1='[\[\e[33m\]\u\[\e[0m\]@\[\e[31m\]\h\[\e[33m\]\[\e[0m\]:\w]% '
EOF

# export the ENV vars globally so they can be available for ssh users at login
printenv | grep -E 'ILIOS_|TRUSTED_PROXIES|DSN' | sed 's/^/export /g' > /etc/profile.d/ecs_env.sh

/bin/echo "Starting ssh server"
/usr/sbin/sshd -D &

/bin/echo "Moving sshd process back to foreground to accept SSH connections..."
fg %1
