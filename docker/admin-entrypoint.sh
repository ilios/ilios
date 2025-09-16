#!/bin/bash
set -m -euf -o pipefail

/bin/echo "Entrypoint ssh-admin container"

# set the PS1 prompt and colorization
cat << EOF >> /root/.bashrc
export PS1='[\[\e[33m\]\u\[\e[0m\]@\[\e[31m\]\h\[\e[33m\]\[\e[0m\]:\w]% '
EOF

if [[ $GITHUB_ACCOUNT_SSH_USERS ]]; then
	# keep a copy of the default file separator
	ORIGINAL_IFS=$IFS

	# Users are separated with semi-colons
	IFS=';'
	for user in $GITHUB_ACCOUNT_SSH_USERS
	do
			/bin/echo "Adding authorized key for user ${user}"
			SSH_DIR="/root/.ssh"
			/usr/bin/wget --quiet -O - "https://github.com/${user}.keys" >> "${SSH_DIR}/authorized_keys"
			/bin/chmod 600 "${SSH_DIR}/authorized_keys"
	done

	IFS=$ORIGINAL_IFS
fi

# export the ENV vars globally so they can be available at session login
printenv | grep -E 'ILIOS_|TRUSTED_PROXIES|APP_|DSN' | sed 's/^/export /g' >> /etc/environment

# Allow user 'root' to login via ssh (off by default)
/usr/bin/sed -i "s/#PermitRootLogin prohibit-password/PermitRootLogin yes/g" /etc/ssh/sshd_config
/bin/echo "Starting ssh server"
/usr/sbin/sshd -D &

/bin/echo "Moving sshd process back to foreground to accept SSH connections..."
fg %1
