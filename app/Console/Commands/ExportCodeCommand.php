<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class ExportCodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:export
                            {filename? : The name of the output file. Defaults to laravel_code_export.txt}
                            {--resources : Include the resources folder.}
                            {--tests : Include the tests folder.}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports application, database, and optional folders into a single text file.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 1. Get the arguments and options from the user's command
        $filename = $this->argument('filename') ?? 'laravel_code_export.txt';
        $includeResources = $this->option('resources');
        $includeTests = $this->option('tests');

        // 2. Define the base paths that are always included
        $pathsToScan = [
            base_path('app'),
            base_path('database'),
        ];

        $this->info("Starting code export...");
        $this->line("Included folders: app, database");

        // 3. Conditionally add optional paths
        if ($includeResources) {
            $pathsToScan[] = base_path('resources');
            $this->line("...including optional folder: resources");
        }
        if ($includeTests) {
            $pathsToScan[] = base_path('tests');
            $this->line("...including optional folder: tests");
        }

        // 4. Use the Finder component for powerful file searching
        $finder = new Finder();
        $finder->in($pathsToScan)
            ->files() // We only want files, not directories
            ->notPath('bootstrap/cache') // Exclude cache
            ->notPath('storage') // Exclude storage
            ->notName('*.log') // Exclude log files
            ->ignoreDotFiles(true) // Ignore files like .DS_Store or .gitignore
            ->ignoreVCS(true); // Ignore version control files/folders like .git

        if (!$finder->hasResults()) {
            $this->error('No files found in the specified directories.');
            return 1; // Indicate an error
        }

        // 5. Prepare the output file
        // We will save the file in the `storage/app` directory.
        $outputFilePath = storage_path('app/' . $filename);

        // Ensure the file is empty before we start writing to it
        File::put($outputFilePath, '');

        $fileCount = 0;

        // 6. Loop through all found files and append them to our master file
        foreach ($finder as $file) {
            $filePath = $file->getRelativePathname();
            $fileContents = $file->getContents();

            // Create a structured block for each file
            $fileBlock = <<<EOD
======================================================================
FILE: {$filePath}
======================================================================

{$fileContents}


EOD;

            // Append the block to our output file
            File::append($outputFilePath, $fileBlock);
            $fileCount++;
        }

        // 7. Provide success feedback to the user
        $this->info("\nExport complete!");
        $this->line("Processed {$fileCount} files.");
        $this->info("All code has been exported to: {$outputFilePath}");

        return 0; // Indicate success
    }
}