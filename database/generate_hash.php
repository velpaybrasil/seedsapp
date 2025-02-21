<?php
$password = 'gugaLima8*';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Senha: $password\n";
echo "Hash: $hash\n";
