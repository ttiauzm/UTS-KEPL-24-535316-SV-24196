<?php

namespace Database\Seeders;

use App\Models\Note;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    public function run(): void
    {
        Note::firstOrCreate(
            ['title' => 'Catatan Pertama'],
            ['content' => 'Ini adalah isi catatan pertama untuk pengujian API Laravel.']
        );

        Note::firstOrCreate(
            ['title' => 'Rencana Integrasi Vue JS'],
            ['content' => 'Menghubungkan endpoint JSON ke frontend Vue menggunakan Axios.']
        );
    }
}
