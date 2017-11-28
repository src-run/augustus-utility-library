<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

/**
 * @param mixed       $variable
 * @param bool        $alsoUseStdOut
 * @param string|null $file
 *
 * @return void
 */
function dump_logged($variable, bool $alsoUseStdOut = false, string $file = null)
{
    $cloner = new \Symfony\Component\VarDumper\Cloner\VarCloner();
    $dumper = new \Symfony\Component\VarDumper\Dumper\CliDumper();

    if (true === $alsoUseStdOut) {
        $dumper->dump($cloner->cloneVar($variable));
    }

    $filesystem = new \Symfony\Component\Filesystem\Filesystem();
    $filesystem->dumpFile((function () use ($file) {
        return $file ?? tempnam(sys_get_temp_dir(), 'var-dumper-');
    })(), $dumper->dump($cloner->cloneVar($variable), true));
}
