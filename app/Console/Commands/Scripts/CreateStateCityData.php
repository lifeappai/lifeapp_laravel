<?php

namespace App\Console\Commands\Scripts;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CreateStateCityData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:state-file {--country=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $country = $this->option('country');

        $response = Http::withHeaders([
            "api-token" => "wRCmRitRjXYexAOXJagpn5S8fek36kGmYfDvJO3dgb9A3qkGMX3YlBUX4B9N6Qvjizo",
            "user-email" => "ceo@magic-lantern.in"
        ])->get("https://www.universal-tutorial.com/api/getaccesstoken");

        $token = $response->json('auth_token');

        $response = Http::withToken($token)->get("https://www.universal-tutorial.com/api/states/" . $country);

        $data = []; //json_decode(file_get_contents(storage_path("$country.json")), true);

         try {
            if ($response->ok()) {
                $states = $response->json();

                $data["states"] = $states;

                foreach ($states as $state) {

                    $stateName = $state["state_name"];

                    if (isset($stateName, $data[$stateName]) &&
                        count($data[$stateName]) > 0) {
                        $this->info("SKIPPED: $stateName");
                        continue;
                    }

                    $this->line($stateName);

                    $response = Http::withToken($token)->get('https://www.universal-tutorial.com/api/cities/' . $stateName);

                    $cities = $response->json();

                    if (is_array($cities)) {
                        $data[$stateName] = $cities;
                    }

                    sleep(5);
                }
            } else {
                $this->line($response->getBody());
            }
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
        }

        $content = json_encode($data);
        $this->line($content);

        file_put_contents(storage_path("$country.json"), $content);

        return 0;
    }
}
