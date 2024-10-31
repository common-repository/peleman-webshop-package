<?php

declare(strict_types=1);

namespace PWP\includes\versionControl;

use PWP\includes\versionControl\VersionNumber;

class ExampleUpdate extends Update
{
        public function __construct(string $version)
        {
            //normally you do not initialize the version number in the constructor as above, instead it should be hard-coded in the script
            //but since this is a testing version and we want to do a bit of experimentation with versioning,
            //this makes it easier to do so.
            //feed the update's version number into the parent constructor here
            parent::__construct($version);

            //do other constructor setup here.
        }

        final public function upgrade() : VersionNumber
        {
            //do upgrade logic here

            //after doing the upgrade logic, return the version number of the upgrade
            //the version controller class will handle updating the local verion number settings
            return $this->get_version_number();
        }

        final public function downgrade(): void
        {
           //do downgrade logic here
           //version controller class will hande updating the local version number settings 
        }
}
