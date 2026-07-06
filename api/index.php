<?php

/**
 * Forward Vercel requests to normal index.php
 */
error_reporting(E_ALL & ~E_DEPRECATED);
require __DIR__ . '/../public/index.php';
