<?php namespace Orchestra\Lumenate;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Studio\Traits\PublishFilesTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeCommand extends Command
{
    use PublishFilesTrait;

    /**
     * The input interface implementation.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * The output interface implementation.
     *
     * @var \Illuminate\Console\OutputStyle
     */
    protected $output;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this->setName('make')
                ->setDescription('Make Lumen skeleton into the current project.')
                ->addOption('force', null, InputOption::VALUE_NONE, 'Overwrite any existing files.');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;

        $filesystem = new Filesystem();

        $paths = $this->getInstallationPaths($filesystem);

        $paths = [
            "{vendor}/orchestra/lumen/skeleton/app" => "{base}/app/Lumen",
            "{vendor}/orchestra/lumen/skeleton/config" => "{base}/lumen/config",
            "{vendor}/orchestra/lumen/skeleton/public" => "{base}/lumen/public",
            "{vendor}/orchestra/lumen/skeleton/artisan" => "{base}/lumen/artisan",
            "{vendor}/orchestra/lumen/skeleton/bootstrap.php" => "{base}/lumen/bootstrap.php",
            "{vendor}/orchestra/lumen/skeleton/server.php" => "{base}/lumen/server.php",
            "{vendor}/orchestra/lumen/skeleton/api.php" => "{base}/routes/api.php",
            "{vendor}/orchestra/lumen/skeleton/lumen.php" => "{base}/routes/lumen.php",
        ];

        $this->publishFiles($filesystem, $paths, $input->getOption('force'));
    }

    /**
     * Get installation paths from lumen.json file.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     *
     * @return array
     */
    protected function getInstallationPaths(Filesystem $filesystem)
    {
        $basePath   = getcwd();
        $vendorPath = "{$basePath}/vendor/orchestra/lumen/skeleton";
        $paths = [];

        if (! $filesystem->isFile($schema = "{$basePath}/lumen.json")) {
            $schema = "{$vendorPath}/lumen.json";
        }

        $paths = json_decode($filesystem->get($schema), true);

        if (is_null($path)) {
            return ["{$vendorPath}" => "{$basePath}/lumen"];
        }

        $paths = array_map(function ($path) use ($vendorPath) {
            return "{$vendorPath}/{$path}";
        }, array_flip($paths));

        return array_map(function ($path) use ($basePath) {
            return "{$basePath}/{$path}";
        }, array_flip($paths));
    }

    /**
     * Write a string as error output.
     *
     * @param  string  $string
     *
     * @return void
     */
    public function error($string)
    {
        $this->output->writeln("<error>$string</error>");
    }

    /**
     * Write a string as standard output.
     *
     * @param  string  $string
     *
     * @return void
     */
    public function line($string)
    {
        $this->output->writeln($string);
    }

    /**
     * Write a status message to the console.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  string  $type
     *
     * @return void
     */
    protected function status($from, $to, $type)
    {
        $from = trim(str_replace(getcwd(), '', realpath($from)), '/');
        $to   = trim(str_replace(getcwd(), '', realpath($to)), '/');

        $this->line('<info>Copied '.$type.'</info> <comment>['.$from.']</comment> <info>To</info> <comment>['.$to.']</comment>');
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     *
     * @return $this
     */
    protected function replaceNamespace($stub)
    {
        return $stub;
    }
}
