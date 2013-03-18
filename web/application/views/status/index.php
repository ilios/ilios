<!DOCTYPE html>
<html>
<head>
    <title>Ilios - Application Status Report</title>
</head>
<body>
    <p>Overall Status: <?php echo $overall ? 'pass' : 'fail'; ?></p>
    <p>Status Details:</p>
    <ul>
<?php foreach ($details as $check => $status) : ?>
       <li><?php echo $check; ?>: <?php echo $status ? 'pass' : 'fail'; ?></li>
<?php endforeach; ?>
    </ul>
</body>
</html>