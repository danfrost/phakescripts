<?php

abstract class Phake_Builder_Script extends Phake_Script
{
    
    final function index() {
        $this->up();
    }
    
    abstract function up();
    
    abstract function down();
}

/*
How the build system works

The phake builder is a sequence of scripts that take some raw source code and build a running site.
It is heavily geared towards web-apps that use a DB connection.

The setup and teardown scripts are all held in a directory called "build":

    ./build/

Inside this sit a series of directories, each for a different stage of the build process.

    1-checks
    2-prebuild
    3-
    ...

And inside each of these directories are the actual build scripts. Each script is a phake script in itself.

The main build command is 'phake build all' which will run through the above directories and run each 
of the scripts inside that directory. Although each stage is run in sequence, the scripts in a particular
directory are run in an arbitrary order.

    1-init/
        Buildsetup.php

Each phake script inside the directory must implement two methods:

    function up() {
    }
    
    function down() {
        
    }

These work in the same way as migrations so that when running 'phake build all', this will run (e.g.)
    phake buildchecks up
    phake symlinks up
    phake buildcreatedb up

*/
class Phake_Script_Build extends Phake_Script
{
    
    const   buildDirName    = 'build';
    
    /**
     * Show help message
     */
    function index($step)
    {
        phake('help build');
    }
    
    private $pretend    = false;
    private $down       = false;
    
    /**
     * Run build sequence. Do --pretend to show just the main phake scripts that would be run. 
     * Add --down to tear down.
     */
    function all($pretend=false, $down=false)
    {
        if($down) {
            echo "DOWN";
            $this->down = 'down';
        }
        $this->pretend = $pretend;
        $this->runScripts($step);
    }
    
    /**
     * Run a specific step
     */
    function step($step=null)
    {
        //echo "step = $step";
        if($step===null) {
            echo "Available steps: ".PHP_EOL;
            foreach (new DirectoryIterator(Phake_Pwd::get().'build/') as $file) {
                if (!$file->isDot() && $file->isDir()) {
                    //phake('init add_local_dir '.Phake_Pwd::get().'build/'.$file);
                    echo '  '.str_replace('-', ': ', $file).PHP_EOL;
                }
            }
            echo PHP_EOL."  Run a step using 'phake step 2' or 'phake step step-name'".PHP_EOL;
            return;
        }
        
        $steps_ints = array();
        $steps_strings = array();
        foreach (new DirectoryIterator(Phake_Pwd::get().'build/') as $file) {
            if (!$file->isDot() && $file->isDir()) {
                //phake('init add_local_dir '.Phake_Pwd::get().'build/'.$file);
                $x = explode('-', $file);
                //echo '  '.str_replace('-', ': ', $file).PHP_EOL;
                $steps_ints[$x[0]] = (string) $file;
                $steps_strings[$x[1]] = (string) $file;
            }
        }
        if(array_key_exists($step, $steps_ints)) {
            $step = $steps_ints[$step];
        } else {
            $step = $steps_strings[$step];
        }
        $this->runScripts($step);
    }
    
    /**
     * Run 
     */
    private function runScripts($step=null)
    {
        $pwd = Phake_Pwd::get();
        
        $dir = $pwd . 'build/' . $step;
        $fs = fs($dir . '*/*.php');
        while($file = $fs->each()) {
            $script = str_replace('.php', '', strtolower($file->getFilename()));
            echo PHP_EOL . "Running: $script" . PHP_EOL;
            if(!$this->pretend) {
                phake($script.' '.($this->down ? 'down' : ''));
            }
        }
    }
    
    
    /**
     * Setup the build directories - this must be done first.
     */
    function init()
    {
        phake('init');
        
        // The template should be here
        phake('gen '.dirname(__FILE__).'/Build/template/ build');
        
        // Add the build dirs to *local* phake dirs
        foreach (new DirectoryIterator(Phake_Pwd::get().'build/') as $file) {
            if (!$file->isDot() && $file->isDir()) {
                phake('init add_local_dir '.Phake_Pwd::get().'build/'.$file);
            }
        }
    }
}

?>