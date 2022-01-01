<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Film extends Model
{
    use HasFactory;

    protected $table = 'films';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'title',
        'original_title',
        'original_title_romanised',
        'image',
        'movie_banner',
        'description',
        'director',
        'producer',
        'release_date',
        'running_time',
        'rt_score',
    ];

    public function getAllFilms() {
        return app('db')->select('select * from films');
    }
    public function getDetailFilmById($id_film) {
        //film
        $sql_films = "
            SELECT
            f.*
            FROM films f
            WHERE
            f.id = ?
        ";
        $film = DB::selectOne($sql_films, [$id_film]);
        if( empty($film) ) {
            return new \stdClass();
        }
        //people
        $sql_people = "
            SELECT
            p.id,
            p.name,
            p.gender,
            p.age,
            p.eye_color,
            p.hair_color
            FROM films f
            INNER JOIN people p ON p.id_film = f.id
            WHERE
            f.id = ?
        ";
        $people = DB::select($sql_people, [$id_film]);
        $film->people = !empty($people)? $people: [];
        //species
        $sql_species = "
            SELECT
            s.id,
            s.name,
            s.classification,
            s.eye_colors,
            s.hair_colors
            FROM films f
            INNER JOIN species s ON s.id_film = f.id
            WHERE
            f.id = ?
        ";
        $species = DB::select($sql_species, [$id_film]);
        $film->species = !empty($species)? $species: [];
        //locations
        $sql_locations = "
            SELECT
            l.id,
            l.name,
            l.climate,
            l.terrain,
            l.surface_water
            FROM films f
            INNER JOIN locations l ON l.id_film = f.id
            WHERE
            f.id = ?
        ";
        $locations = DB::select($sql_locations, [$id_film]);
        $film->locations = !empty($locations)? $locations: [];
        //vehicles
        $sql_vehicles = "
            SELECT
            v.id,
            v.name,
            v.description,
            v.vehicle_class,
            v.length
            FROM films f
            INNER JOIN vehicles v ON v.id_film = f.id
            WHERE
            f.id = ?
        ";
        $vehicles = DB::select($sql_vehicles, [$id_film]);
        $film->vehicles = !empty($vehicles)? $vehicles: [];
        return $film;
    }
}
