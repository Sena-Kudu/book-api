<?php

namespace Config;
use CodeIgniter\Config\BaseConfig;

class JwtConfig extends BaseConfig{

    public $JWT_SECRET_KEY  = 'kzUf4sxss4AeG5uHkNZAqT1Nyi1zVfpz';
    public $JWT_TIME_TO_LIVE = 36000;

}
