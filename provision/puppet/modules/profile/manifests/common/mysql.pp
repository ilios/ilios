class profile::common::mysql (
    $mysql_users = {},
    $mysql_grants = {},
    $bind_address = '0.0.0.0'
) {
    class { 'mysql::server':
        users            => $mysql_users,
        grants           => $mysql_grants,
        override_options => { 'mysqld' => { 'bind_address' => $bind_address } }
    }
}
