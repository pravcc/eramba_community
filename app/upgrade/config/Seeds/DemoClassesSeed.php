<?php
use Migrations\AbstractSeed;

use Migrations\Command\Seed;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;

/**
 * DemoClasses seed.
 */
class DemoClassesSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $this->input->setOption('source', 'Seeds/DemoSeeders');

        $path = __DIR__ . DIRECTORY_SEPARATOR . 'DemoSeeders';

        $files = glob($path . '/*.php');
        $files = array_map([$this, 'files'], $files);
        foreach ($files as $file) {
            $this->call($file);
        }
    }

    public function files($val) {
        return str_replace('.php', '', basename($val));
    }
}
