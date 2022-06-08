<?php

namespace App\Imports;

use App\Models\Person;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class HomeownerNames implements ToModel, WithProgressBar, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        $namesSplit = collect(explode(' ', $row['homeowner']));

        if ($namesSplit->contains('&') || $namesSplit->contains('and')) {
            $this->processMultiplePeople($namesSplit);
        } else {
            $this->processSinglePerson($namesSplit);
        }
    }

    private function processSinglePerson($namesSplit)
    {
        $person = [
            'title' => $namesSplit->first(),
            'initial' => null,
            'first_name' => null,
            'last_name' => null,
        ];

        $secondWord = str_replace('.', '', $namesSplit->get(1));
        if (strlen($secondWord) == 1) {
            $person['initial'] = $secondWord;
        } else {
            $person['first_name'] = $secondWord;
        }

        $person['last_name'] = $namesSplit->last();

        $this->savePerson($person);
    }

    private function processMultiplePeople(Collection $namesSplit)
    {
        $person1 = [
            'title' => $namesSplit->first(),
            'initial' => null,
            'first_name' => null,
            'last_name' => null,
        ];

        $person2 = [
            'title' => null,
            'initial' => null,
            'first_name' => null,
            'last_name' => null,
        ];

        if (in_array($namesSplit->get(1), ['and', '&'])) {
            $person2['title'] = $namesSplit->get(2);
            if ($namesSplit->count() == 5) {
                $person1['first_name'] = $namesSplit->get(3);
            }
            $person1['last_name'] = $person2['last_name'] = $namesSplit->last();

        } else {
            $first = $namesSplit->slice(0, $namesSplit->search('and'));
            $second = $namesSplit->slice($namesSplit->search('and') + 1)->values();

            $person1['first_name'] = $first->get(1);
            $person1['last_name'] = $first->get(2);

            $person2['title'] = $second->get(0);
            $person2['first_name'] = $second->get(1);
            $person2['last_name'] = $second->get(2);

        }

        $this->savePerson($person1);
        $this->savePerson($person2);
    }

    private function savePerson(array $data)
    {
        Person::create($data);
    }
}
