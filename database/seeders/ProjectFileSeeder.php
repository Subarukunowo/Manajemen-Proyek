<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectFileSeeder extends Seeder
{
    public function run(): void
    {
        $files = [
            [
                'id' => 1,
                'project' => 'Project Title 1',
                'file_name' => 'File 1',
                'file_path' => 'File Path 1',
                'extension' => 'jpg',
            ],
            [
                'id' => 2,
                'project' => 'Project Title 3',
                'file_name' => 'File 2',
                'file_path' => 'File Path 2',
                'extension' => 'xlsm',
            ],
            [
                'id' => 3,
                'project' => 'Project Title 4',
                'file_name' => 'File 3',
                'file_path' => 'File Path 3',
                'extension' => 'docx',
            ],
            [
                'id' => 4,
                'project' => 'Project Title 5',
                'file_name' => 'File 4',
                'file_path' => 'File Path 4',
                'extension' => 'xlsx',
            ],
            [
                'id' => 5,
                'project' => 'Project Title 6',
                'file_name' => 'File 5',
                'file_path' => 'File Path 5',
                'extension' => 'xlsb',
            ],
            [
                'id' => 6,
                'project' => 'Project Title 7',
                'file_name' => 'File 6',
                'file_path' => 'File Path 6',
                'extension' => 'doc',
            ],
            [
                'id' => 7,
                'project' => 'Project Title 1',
                'file_name' => 'File 7',
                'file_path' => 'File Path 7',
                'extension' => 'xls',
            ],
            [
                'id' => 8,
                'project' => 'Project Title 3',
                'file_name' => 'File 8',
                'file_path' => 'File Path 8',
                'extension' => 'png',
            ],
            [
                'id' => 9,
                'project' => 'Project Title 4',
                'file_name' => 'File 9',
                'file_path' => 'File Path 9',
                'extension' => 'jpg',
            ],
            [
                'id' => 10,
                'project' => 'Project Title 5',
                'file_name' => 'File 10',
                'file_path' => 'File Path 10',
                'extension' => 'xlsm',
            ],
            [
                'id' => 11,
                'project' => 'Project Title 6',
                'file_name' => 'File 11',
                'file_path' => 'File Path 11',
                'extension' => 'xlsm',
            ],
            [
                'id' => 12,
                'project' => 'Project Title 7',
                'file_name' => 'File 12',
                'file_path' => 'File Path 12',
                'extension' => 'docx',
            ],
            [
                'id' => 13,
                'project' => 'Project Title 1',
                'file_name' => 'File 13',
                'file_path' => 'File Path 13',
                'extension' => 'xlsx',
            ],
            [
                'id' => 14,
                'project' => 'Project Title 3',
                'file_name' => 'File 14',
                'file_path' => 'File Path 14',
                'extension' => 'doc',
            ],
            [
                'id' => 15,
                'project' => 'Project Title 4',
                'file_name' => 'File 15',
                'file_path' => 'File Path 15',
                'extension' => 'xls',
            ],
            [
                'id' => 16,
                'project' => 'Project Title 5',
                'file_name' => 'File 16',
                'file_path' => 'File Path 16',
                'extension' => 'xlst',
            ],
            [
                'id' => 17,
                'project' => 'Project Title 6',
                'file_name' => 'File 17',
                'file_path' => 'File Path 17',
                'extension' => 'png',
            ],
            [
                'id' => 18,
                'project' => 'Project Title 7',
                'file_name' => 'File 18',
                'file_path' => 'File Path 18',
                'extension' => 'svg',
            ],
            [
                'id' => 19,
                'project' => 'Project Title 8',
                'file_name' => 'File 19',
                'file_path' => 'File Path 19',
                'extension' => 'xlsm',
            ],
            [
                'id' => 20,
                'project' => 'Project Title 1',
                'file_name' => 'File 20',
                'file_path' => 'File Path 20',
                'extension' => 'xlsm',
            ],
            [
                'id' => 21,
                'project' => 'Project Title 3',
                'file_name' => 'File 21',
                'file_path' => 'File Path 21',
                'extension' => 'docx',
            ],
            [
                'id' => 22,
                'project' => 'Project Title 3',
                'file_name' => 'File 22',
                'file_path' => 'File Path 22',
                'extension' => 'xlsx',
            ],
            [
                'id' => 23,
                'project' => 'Project Title 3',
                'file_name' => 'File 23',
                'file_path' => 'File Path 23',
                'extension' => 'doc',
            ],
            [
                'id' => 24,
                'project' => 'Project Title 3',
                'file_name' => 'File 24',
                'file_path' => 'File Path 24',
                'extension' => 'xls',
            ],
            [
                'id' => 25,
                'project' => 'Project Title 3',
                'file_name' => 'File 25',
                'file_path' => 'File Path 25',
                'extension' => 'xlst',
            ],
            [
                'id' => 26,
                'project' => 'Project Title 3',
                'file_name' => 'File 26',
                'file_path' => 'File Path 26',
                'extension' => 'png',
            ],
            [
                'id' => 27,
                'project' => 'Project Title 3',
                'file_name' => 'File 27',
                'file_path' => 'File Path 27',
                'extension' => 'bas',
            ],
            [
                'id' => 28,
                'project' => 'Project Title 10',
                'file_name' => 'File 28',
                'file_path' => 'File Path 28',
                'extension' => 'Link',
            ],
            [
                'id' => 29,
                'project' => 'Project Title 10',
                'file_name' => 'File 29',
                'file_path' => 'File Path 29',
                'extension' => 'Link',
            ],
            [
                'id' => 30,
                'project' => 'Project Title 6',
                'file_name' => 'File 30',
                'file_path' => 'File Path 30',
                'extension' => 'Link',
            ],
            [
                'id' => 31,
                'project' => 'Project Title 6',
                'file_name' => 'File 31',
                'file_path' => 'File Path 31',
                'extension' => 'jpg',
            ],
            [
                'id' => 32,
                'project' => 'Project Title 8',
                'file_name' => 'File 32',
                'file_path' => 'File Path 32',
                'extension' => 'Link',
            ],
            [
                'id' => 33,
                'project' => 'Project Title 2',
                'file_name' => 'File 33',
                'file_path' => 'File Path 33',
                'extension' => 'jpg',
            ],
            [
                'id' => 34,
                'project' => 'Project Title 2',
                'file_name' => 'File 34',
                'file_path' => 'File Path 34',
                'extension' => 'xml',
            ],
            [
                'id' => 35,
                'project' => 'Project Title 2',
                'file_name' => 'File 35',
                'file_path' => 'File Path 35',
                'extension' => 'docx',
            ],
            [
                'id' => 36,
                'project' => 'Project Title 2',
                'file_name' => 'File 36',
                'file_path' => 'File Path 36',
                'extension' => 'xlsx',
            ],
        ];

        foreach ($files as $file) {
            DB::table('project_files')->updateOrInsert(['id' => $file['id']], $file);
        }
    }
}
