<?php

$container->setParameter('user_home', getenv('HOME'));

if (file_exists($path = getenv('HOME').'/.expense-manager/config.json')) {
    $config = json_decode(
        file_get_contents($path),
        true
    );
    $walletPath = $config['wallet_path'];
} else {
    $walletPath = dirname($path).'/wallet';
}

$container->setParameter('wallet_path', $walletPath);
