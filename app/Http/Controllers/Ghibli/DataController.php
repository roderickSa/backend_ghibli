<?php

namespace App\Http\Controllers\Ghibli;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DataController extends Controller {

    public function __construct() {
        $this->middleware(
            'auth:api'
        );
    }

    public function index() {
        set_time_limit(60 * 60);
        $data = $this->getData();
        $this->insertData($data);
        // print_r($result);exit;
        return response()->json(['data' => 'echo']);
    }

    private function getCurl($url) {
        $cliente = curl_init();
        curl_setopt($cliente, CURLOPT_URL, $url);
        curl_setopt($cliente, CURLOPT_HEADER, false);
	    curl_setopt($cliente, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cliente, CURLOPT_SSL_VERIFYPEER, false); // For HTTPS
        curl_setopt($cliente, CURLOPT_SSL_VERIFYHOST, false); // For HTTPS
        $data = json_decode(curl_exec($cliente));
        curl_close($cliente);
        return $data;
    }
    private function getData() {
        $url_films = "https://ghibliapi.herokuapp.com/films";
        $films = $this->getCurl($url_films);
        if( count($films) > 0 ) {
            foreach ($films as $key => $film) {
                //people
                if($film->people) {
                    foreach ($film->people as $key => $url_people) {
                        if( $url_people ){
                            $response_people = $this->getCurl($url_people);
                            if($response_people) {
                                $film->_people[] = $response_people;
                            }
                        }
                    }
                }
                //species
                if($film->species) {
                    foreach ($film->species as $key => $url_species) {
                        if( $url_species ){
                            $response_species = $this->getCurl($url_species);
                            if($response_species) {
                                $film->_species[] = $response_species;
                            }
                        }
                    }
                }
                //locations
                if($film->locations) {
                    foreach ($film->locations as $key => $url_locations) {
                        if( $url_locations ){
                            $response_locations = $this->getCurl($url_locations);
                            if($response_locations) {
                                $film->_locations[] = $response_locations;
                            }
                        }
                    }
                }
                //vehicles
                if($film->vehicles) {
                    foreach ($film->vehicles as $key => $url_vehicles) {
                        if( $url_vehicles ){
                            $response_vehicles = $this->getCurl($url_vehicles);
                            if($response_vehicles) {
                                $film->_vehicles[] = $response_vehicles;
                            }
                        }
                    }
                }
            }
        }
        return $films;
    }
    private function insertData($data) {
        foreach ($data as $key => $film) {
            #insert film
            $film->title = addslashes($film->title);
            $film->original_title = addslashes($film->original_title);
            $film->description = addslashes($film->description);
            $sql_film = "insert into films(
                id,
                title,
                original_title,
                original_title_romanised,
                image,
                movie_banner,
                description,
                director,
                producer,
                release_date,
                running_time,
                rt_score
                )
                values(
                    '$film->id',
                    '$film->title',
                    '$film->original_title',
                    '$film->original_title_romanised',
                    '$film->image',
                    '$film->movie_banner',
                    '$film->description',
                    '$film->director',
                    '$film->producer',
                    '$film->release_date',
                    '$film->running_time',
                    '$film->rt_score'
                )";
            app('db')->insert($sql_film);
            // $id_film = app('db')->connection()->getPdo()->lastInsertId();
            $id_film = $film->id;
            if($id_film) {
                #insert people
                foreach ($film->_people as $key => $peoples) {
                    if( gettype($peoples) == 'array' ) {
                        foreach ($peoples as $key => $people) {
                            $people->gender = !empty($people->gender)? $people->gender: '';
                            $sql_people = "
                                insert into people(
                                    id,
                                    name,
                                    gender,
                                    age,
                                    eye_color,
                                    hair_color,
                                    id_film
                                )
                                values(
                                    '$people->id',
                                    '$people->name',
                                    '$people->gender',
                                    '$people->age',
                                    '$people->eye_color',
                                    '$people->hair_color',
                                    '$id_film'
                                )
                            ";
                            app('db')->insert($sql_people);
                        }
                    }else{
                        $peoples->gender = !empty($peoples->gender)? $peoples->gender: '';
                        $sql_people = "
                            insert into people(
                                id,
                                name,
                                gender,
                                age,
                                eye_color,
                                hair_color,
                                id_film
                            )
                            values(
                                '$peoples->id',
                                '$peoples->name',
                                '$peoples->gender',
                                '$peoples->age',
                                '$peoples->eye_color',
                                '$peoples->hair_color',
                                '$id_film'
                            )
                        ";
                        app('db')->insert($sql_people);
                    }
                }
                #insert species
                foreach ($film->_species as $key => $species) {
                    if( gettype($species) == 'array' ) {
                        foreach ($species as $key => $specie) {
                            $sql_species = "
                                insert into species(
                                    id,
                                    name,
                                    classification,
                                    eye_colors,
                                    hair_colors,
                                    id_film
                                )
                                values(
                                    '$specie->id',
                                    '$specie->name',
                                    '$specie->classification',
                                    '$specie->eye_colors',
                                    '$specie->hair_colors',
                                    '$id_film'
                                )
                            ";
                            app('db')->insert($sql_species);
                        }
                    }else{
                        $sql_species = "
                            insert into species(
                                id,
                                name,
                                classification,
                                eye_colors,
                                hair_colors,
                                id_film
                            )
                            values(
                                '$species->id',
                                '$species->name',
                                '$species->classification',
                                '$species->eye_colors',
                                '$species->hair_colors',
                                '$id_film'
                            )
                        ";
                        app('db')->insert($sql_species);
                    }
                }
                #insert locations
                foreach ($film->_locations as $key => $locations) {
                    if( gettype($locations) == 'array' ) {
                        foreach ($locations as $key => $location) {
                            $location->name = addslashes($location->name);
                            $sql_locations = "
                                insert into locations(
                                    id,
                                    name,
                                    climate,
                                    terrain,
                                    surface_water,
                                    id_film
                                )
                                values(
                                    '$location->id',
                                    '$location->name',
                                    '$location->climate',
                                    '$location->terrain',
                                    '$location->surface_water',
                                    '$id_film'
                                )
                            ";
                            app('db')->insert($sql_locations);
                        }
                    }else{
                        $locations->name = addslashes($locations->name);
                            $sql_locations = "
                                insert into locations(
                                    id,
                                    name,
                                    climate,
                                    terrain,
                                    surface_water,
                                    id_film
                                )
                                values(
                                    '$locations->id',
                                    '$locations->name',
                                    '$locations->climate',
                                    '$locations->terrain',
                                    '$locations->surface_water',
                                    '$id_film'
                                )
                            ";
                            app('db')->insert($sql_locations);
                    }
                }
                #insert vehicles
                foreach ($film->_vehicles as $key => $vehicles) {
                    if( gettype($vehicles) == 'array' ) {
                        foreach ($vehicles as $key => $vehicle) {
                            $vehicle->name = addslashes($vehicle->name);
                            $vehicle->description = addslashes($vehicle->description);
                            $sql_vehicles = "
                                insert into vehicles(
                                    id,
                                    name,
                                    description,
                                    vehicle_class,
                                    length,
                                    id_film
                                )
                                values(
                                    '$vehicle->id',
                                    '$vehicle->name',
                                    '$vehicle->description',
                                    '$vehicle->vehicle_class',
                                    '$vehicle->length',
                                    '$id_film'
                                )
                            ";
                            app('db')->insert($sql_vehicles);
                        }
                    }else{
                        $vehicles->name = addslashes($vehicles->name);
                        $vehicles->description = addslashes($vehicles->description);
                            $sql_vehicles = "
                                insert into vehicles(
                                    id,
                                    name,
                                    description,
                                    vehicle_class,
                                    length,
                                    id_film
                                )
                                values(
                                    '$vehicles->id',
                                    '$vehicles->name',
                                    '$vehicles->description',
                                    '$vehicles->vehicle_class',
                                    '$vehicles->length',
                                    '$id_film'
                                )
                            ";
                            app('db')->insert($sql_vehicles);
                    }
                }
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Data  $data
     * @return \Illuminate\Http\Response
     */
    public function show(Data $data)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Data  $data
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Data $data)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Data  $data
     * @return \Illuminate\Http\Response
     */
    public function destroy(Data $data)
    {
        //
    }
}
