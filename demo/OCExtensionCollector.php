<?php
/**
 * Created by PhpStorm.
 * User: lolwe
 * Date: 10/24/18
 * Time: 8:04 PM
 */


/**
 * Class OCExtensionCollector
 */
class OCExtensionCollector {

    /**
     * @var string|null Extension title
     */
    private $title;

    /**
     * @var string Storage location of the assembled plugin
     */
    private $outputFolder;

    /**
     * @var string OpenCart installation path
     */
    private $OCPath;

    /**
     * @var bool Flag for use sub-file
     */
    private $useSubFile;

    /**
     * @var string|null Should contain a sub-file path
     */
    private $subFile;

    /**
     * @var array|null Should contain looking file names
     */
    private $lookingFilenames;

    /**
     * @var string contain OC project path
     */
    private $OCpath;

    /**
     * @var array|null Should contain all found extension file names
     */
    private $extensionFilenames;

    /**
     * OCExtensionCollector constructor.
     * @throws Exception
     */
    public function __construct($title = null) {

        $this->title = $title;

        $this->extensionFilenames = array();

        $this->setOCPath();
    }

    /**
     * Set path to OC project
     *
     * @param string $path OC project path
     */
    public function setOCPath($path = 'upload') {
        $this->OCpath = $path;
    }

    /**
     * @param bool $flag Set true if collector require sub file with additional file paths
     */
    public function useSubFile($flag = false) {
        $this->useSubFile = $flag;
    }

    /**
     * Set sub-file
     *
     * @param null|string $subFile
     * @throws Exception
     */
    public function setSubFile($subFile)
    {
        if ( $this->useSubFile ) {
            $this->subFile = $subFile;
        } else {
            throw new Exception("Set useSubFile(true) for using sub-file functionality");
        }
    }

    /**
     *
     * @return null|string
     * @throws Exception
     */
    private function getSubFile()
    {
        if ( $this->useSubFile ) {
            if ( is_file($this->subFile) ) {
                return $this->subFile;
            } else {
                throw new Exception("Sub-file not exists");
            }
        } else {
            return false;
        }

    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getOCPath()
    {
        return $this->OCPath;
    }

    /**
     * @param string $outputFolder
     */
    public function setOutputFolder($outputFolder)
    {
        $this->outputFolder = $outputFolder;
    }

    /**
     * @return string
     */
    public function getOutputFolder()
    {
        return $this->outputFolder;
    }


    /**
     * Collect looking filenames
     * Files will collect by extension title
     * And sub-file if it use
     */
    public function collectLookingFilenames()
    {

        if ( $this->title ) {
            $this->lookingFilenames[] = $this->title;
        }

        if ( $this->useSubFile ) {
            try {
                $fp = fopen($this->getSubFile(), 'r');

                if ($fp) {
                    while (($line = fgets($fp)) !== false) {
                        $this->lookingFilenames[] = trim($line);
                    }
                }

                fclose($fp);

            } catch (Exception $e) {
                die( $e->getMessage() );
            }
        }
    }

    /**
     * Start collecting files
     */
    public function collect() {
        $this->collectExtensionFiles();
        $this->copyOutputFiles();
    }

    /**
     * Collect all extension filenames
     *
     * @return array|null All found extension file names
     */
    private function collectExtensionFiles() {



        $this->collectLookingFilenames();

        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->OCpath));

        foreach ($rii as $file) {

            if ($file->isDir()){
                continue;
            }

            if ($file->isFile()){

                foreach ( $this->lookingFilenames as $lookingFilename ) {

                    if( strpos($file->getPathname(), $lookingFilename) !== false ) {
                        $this->extensionFilenames[] = $file->getPathname();
                    }

                }

            }
        }
        return $this->extensionFilenames;

        //var_dump($this->extensionFilenames);
    }

    private function copyOutputFiles() {

        foreach ( $this->extensionFilenames as $extensionFilename ) {

            if (isset($this->outputFolder)) {
                $extension_dir = $this->outputFolder;
            } else {
                $extension_dir = ($this->title) ? $this->title : 'extension';
            }

            $output_file = $extension_dir . '/' . $extensionFilename;

            $path = pathinfo($output_file);

            if ( !file_exists($output_file) ) {
                @mkdir($path['dirname'], 0777, true);
            }

            if ( !copy($extensionFilename,$output_file) ) {
                echo "Copy failed \n";
            }

        }
    }
}