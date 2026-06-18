<?php

namespace Tests\Integration\Bencana;

use Tests\Integration\IntegrationTestCase;
use App\Models\Bencana;

class PencarianBencanaTest extends IntegrationTestCase
{
    public function test_api_peta_bencana_mengembalikan_koordinat_dan_data_valid()
    {
        $bencana = $this->createBencanaAktif();

        $response = $this->getJson('/api/bencana/peta');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $bencana->id,
            'nama_bencana' => $bencana->nama_bencana,
        ]);
        
        // Assert structure contains latitude and longitude
        $response->assertJsonStructure([
            '*' => ['id', 'nama_bencana', 'latitude', 'longitude', 'status_siaga']
        ]);
    }

    public function test_pencarian_dan_filter_bencana()
    {
        Bencana::factory()->create([
            'nama_bencana' => 'Gempa Bumi Jogja',
            'jenis_bencana' => 'gempa',
            'lokasi' => 'Yogyakarta',
            'status_siaga' => 'siaga',
            'status_aktif' => true,
        ]);

        Bencana::factory()->create([
            'nama_bencana' => 'Banjir Bandang Jakarta',
            'jenis_bencana' => 'banjir',
            'lokasi' => 'Jakarta',
            'status_siaga' => 'awas',
            'status_aktif' => true,
        ]);

        // Cari berdasarkan jenis = banjir
        $response = $this->getJson('/api/bencana?jenis=banjir');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Banjir Bandang Jakarta', $data[0]['nama_bencana']);

        // Cari berdasarkan tingkat siaga = siaga
        $response2 = $this->getJson('/api/bencana?siaga=siaga');
        $response2->assertStatus(200);
        $data2 = $response2->json('data');
        $this->assertCount(1, $data2);
        $this->assertEquals('Gempa Bumi Jogja', $data2[0]['nama_bencana']);
    }
}
