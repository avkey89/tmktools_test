<?php

namespace App\Command;

use App\Services\CategoryService;
use App\Services\ProductService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Response;

class ParserCommand extends Command
{
    protected static $defaultName = 'app:parser';
    private $categoryService;
    private $productService;

    public function __construct(CategoryService $categoryService, ProductService $productService)
    {
        $this->categoryService = $categoryService;
        $this->productService = $productService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('This command adds and updates categories/products by file.')
            ->addOption('fileinfo', null, InputOption::VALUE_NONE, 'Information about file: size, count rows')
        ;
        //->addArgument('path', InputArgument::REQUIRED, 'Path to file (relative to the root)')
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $DOCUMENT_ROOT = realpath(dirname(__FILE__)."/../..");
        $io = new SymfonyStyle($input, $output);

        $helper = $this->getHelper('question');
        $getParserType = new ChoiceQuestion('Please select type data'."\n"."> ", ['category', 'product']);
        $parserType = $helper->ask($input, $output, $getParserType);

        $defaultPath = [
            "category" => "/var/upload/categories.json",
            "product" => "/var/upload/products.json"
        ];

        $getPath = new Question('Please input path to file (relative to the root) [default: '.$defaultPath[$parserType].']'."\n"."> ", $defaultPath[$parserType]);
        $path = $helper->ask($input, $output, $getPath);

        if (!$path) {
            $io->warning('Please check you path to file');
            return false;
        }
        //$path = $input->getArgument('path');

        $file = $DOCUMENT_ROOT;
        if ($path) {
            $file = $DOCUMENT_ROOT . $path;
            if (!file_exists($file)) {
                $io->warning(sprintf('File or path not found: %s. Please check you input', $path));
                return false;
            }
        }

        if ($input->getOption('fileinfo')) {
            $io->writeln("File size: ". round(filesize($file) / 1024, 2) ."mb");
            $rows = file($file);
            $io->writeln("Count rows in file: ".count($rows));
        }

        $data = json_decode(file_get_contents($file), true);
        if (!empty($data)) {
            if ($parserType == "category") {
                $service = $this->categoryService;
            }
            elseif ($parserType == "product") {
                $service = $this->productService;
            } else {
                $io->error('Parser type not found.');
                return false;
            }
            foreach($data as $array) {
                $response = $service->add($array);
                switch($response["status"]) {
                    case Response::HTTP_OK:
                        $io->success($parserType." '".$array["eId"]." - ".$array["title"]."': ".$response["response"]["message"]." Id in system: ".$response["response"]["id"]."");
                        break;

                    case Response::HTTP_BAD_REQUEST:
                    case Response::HTTP_INTERNAL_SERVER_ERROR:
                        $io->error($parserType." '".$array["eId"]." - ".$array["title"]."' didn't add: ".$response["response"]["message"]);
                        break;

                    default:
                        break;
                }
            }
        }

        $io->success('Command executed!');

        return 0;
    }
}
