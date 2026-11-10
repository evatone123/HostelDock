<?php

namespace Database\Seeders;

use App\Models\Hostel;
use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Database\Seeder;

class HostelSeeder extends Seeder
{
    protected $coordinates = [
        'anglican-hostel-(knust,-kumasi-campus)-34' => [6.675090, -1.559650],
        'banivillas-hostel-(kstu,-adako-jachie-campus)-48' => [6.694023, -1.610251],
        'beta-test-hostel-(test-hostel,-do-not-book)-51' => [6.692220, -1.546390],
        'celia-royale-hostel-(knust,-kumasi-campus)-30' => [6.694023, -1.610251],
        'classic-view-hostel-(kstu,-main-campus)-62' => [6.679280, -1.610070],
        'crystal-rose-hostel-(knust,-kumasi-campus)-33' => [6.675090, -1.559650],
        'danleo-hostel-(knust,-kumasi-campus)-32' => [6.675090, -1.559650],
        'destiny-view-hostel-(knust,-kumasi-campus)-40' => [6.673175, -1.565423],
        'dk-appiah-hostel-(knust,-kumasi-campus)-44' => [6.678100, -1.542200],
        'emmal-ville-hostel-(knust,-kumasi-campus)-35' => [6.675090, -1.559650],
        'evandy-hostel-(knust,-kumasi-campus)-29' => [6.675090, -1.559650],
        'ezmil-hostel-(knust,-kumasi-campus)-83' => [6.675090, -1.559650],
        'georgia-hostel-(knust,-kumasi-campus)-47' => [6.673175, -1.565423],
        'ghana-hostels-(knust,-kumasi-campus)-59' => [6.673175, -1.565423],
        'honesty-hostel-(knust,-kumasi-campus)-43' => [6.673175, -1.565423],
    ];

    protected $roomTypeMult = [
        'Six in a room' => 1.00,
        'Five in a room' => 1.00,
        'Four in a room' => 1.00,
        'Three in a room' => 1.15,
        'Two in a room' => 1.25,
        'One in a room' => 1.40,
    ];

    protected $hostels = [
        [
            'name' => 'ANGLICAN HOSTEL (KNUST, KUMASI CAMPUS)',
            'slug' => 'anglican-hostel-(knust,-kumasi-campus)-34',
            'institution' => 'knust-kumasi-campus',
            'institution_name' => 'KNUST KUMASI CAMPUS',
            'location' => 'Ayeduasi - Ashanti Region',
            'distance' => '3KM FROM CAMPUS',
            'rating' => 1.0,
            'starting_price' => 3050,
            'male' => 0,
            'female' => 1,
            'unisex' => 15,
            'images' => [
                'pXfwuEVj8mwugikZuDDAFmVrYG192FqKFYIToGml.jpg',
                'YZz7DBJNnFXkOFOsK5m8PPAuSldpdpBNVJRWn9t6.jpg',
                'CbIhQQh3f22ymNgeYp2EPNNph26ulpZ6rIHeB0Mo.jpg',
                'Mf8FOx0ifWfQTZM1wVOztLUTk7Bgm9fae9IYJ1KK.jpg',
                '8S0QL5BU1jwVppl7IoFaufBUb7Vr9hvJslITr8Et.jpg',
            ],
        ],
        [
            'name' => 'BANIVILLAS HOSTEL (KSTU, ADAKO JACHIE CAMPUS)',
            'slug' => 'banivillas-hostel-(kstu,-adako-jachie-campus)-48',
            'institution' => 'kumasi-technical-university',
            'institution_name' => 'KUMASI TECHNICAL UNIVERSITY (KSTU)',
            'location' => 'Kumasi - Ashanti Region',
            'distance' => '18 Mins Drive to Adako Jachie Campus',
            'rating' => 0.0,
            'starting_price' => 6825,
            'male' => 0,
            'female' => 0,
            'unisex' => 4,
            'images' => [
                'hh8zPlFeXS24YgpQ2aLDQqglS2IubihlvdYf1sEn.jpg',
                'ArWzn82lZrteUKGEVzlzFe7ZJb4MYMSS79Jk72jR.jpg',
                'WK16Nqj2xKnKXO37Q8Ic4ck3QZuEZQLysHaRKVZA.jpg',
            ],
        ],
        [
            'name' => 'BETA TEST HOSTEL (TEST HOSTEL, DO NOT BOOK)',
            'slug' => 'beta-test-hostel-(test-hostel,-do-not-book)-51',
            'institution' => 'knust-kumasi-campus',
            'institution_name' => 'KNUST KUMASI CAMPUS',
            'location' => 'Oduom - Ashanti Region',
            'distance' => '500M WALK TO CAMPUS',
            'rating' => 2.0,
            'starting_price' => 1,
            'male' => 9,
            'female' => 2,
            'unisex' => 2,
            'images' => [
                'TQkBGGd8cjYL3uibO9UISrxbOjN9ws8qkCeSq0Pt.png',
                'ZoteMV23ZwmTfQKsDx8eeNr1cpbUD903oiSmEoqx.png',
                'yEGuJaxNIEjiFTXub0QbRiEsbnGmFRGMKxyoGCrd.jpg',
            ],
        ],
        [
            'name' => 'CELIA ROYALE HOSTEL (KNUST, KUMASI CAMPUS)',
            'slug' => 'celia-royale-hostel-(knust,-kumasi-campus)-30',
            'institution' => 'knust-kumasi-campus',
            'institution_name' => 'KNUST KUMASI CAMPUS',
            'location' => 'Kumasi - Ashanti Region',
            'distance' => '600M Walk',
            'rating' => 5.0,
            'starting_price' => 3600,
            'male' => 5,
            'female' => 2,
            'unisex' => 2,
            'images' => [
                'JmxkdwAWG5N7PfeyhtDCRoVqVMSLF4LU4iHI8cWX.png',
                '6nIlzZWhO0OyNN42XOzijqTuXn5l989oMbc4dA0G.jpg',
                'ZhZrcxJ416AtfNs1aJMVNV9EewIcde7ewRmugsF9.jpg',
                'UMHAarqJ6YYu8JFjNCRdrLuSHzuVF6SUyG7BaS9u.jpg',
            ],
        ],
        [
            'name' => 'CLASSIC VIEW HOSTEL (KSTU, MAIN CAMPUS)',
            'slug' => 'classic-view-hostel-(kstu,-main-campus)-62',
            'institution' => 'kumasi-technical-university',
            'institution_name' => 'KUMASI TECHNICAL UNIVERSITY (KSTU)',
            'location' => 'Amakom - Ashanti Region',
            'distance' => '2mins walk to Campus',
            'rating' => 1.0,
            'starting_price' => 6000,
            'male' => 0,
            'female' => 0,
            'unisex' => 35,
            'images' => [
                'fnovzQEudZy0MULWbttufIn4wPmSJDQW6mBDE5tP.jpg',
                'McXOOa9ghRZJmyW2k3rREcmey61YQBkuFFiN9Z93.jpg',
                'Z68RN9QCMDRFqe1HY1Si0ewZgjIoXHcxTKoadENy.jpg',
                'CubwkKxEoUxvYofn2AU4IpR3LxMZthWDdWmKg3nz.jpg',
                '0eNxmVF0jDIgnf1dAVulIQGqXzdQIHYKtQ2Rflws.jpg',
                'ENzPb3FEaRQYo81qphqSNLL5HahOujOHfkee1L2X.jpg',
            ],
        ],
        [
            'name' => 'CRYSTAL ROSE HOSTEL (KNUST, KUMASI CAMPUS)',
            'slug' => 'crystal-rose-hostel-(knust,-kumasi-campus)-33',
            'institution' => 'knust-kumasi-campus',
            'institution_name' => 'KNUST KUMASI CAMPUS',
            'location' => 'Ayeduasi - Ashanti Region',
            'distance' => '5 MINUTES DRIVE TO CAMPUS',
            'rating' => 5.0,
            'starting_price' => 1,
            'male' => 20,
            'female' => 16,
            'unisex' => 0,
            'images' => [
                'Y9yRF73nM1OOrcW92fiDcMO1GEYpTnzBBsDapYQ8.jpg',
                '1uVZPvSXzZz8kfBxvsYuvGOxrV0V4SRonvF13qst.jpg',
                'klnpUDxdokNziIz3nKtyqxNiGWqYsuPb9VqvCr5u.jpg',
                '0lCWGpKyakn3mmDFxeWJXHxSOK6ycCi2hd2zoA0T.jpg',
                'iqX24eJH53PO4qhyytxiPdyhRwH8ohZG9h8Dllkb.jpg',
                'HdNKaiW1XDoGKOX79H0xV0LZLZ2z0PV5ML8wnWAu.jpg',
            ],
        ],
        [
            'name' => 'DANLEO HOSTEL (KNUST, KUMASI CAMPUS)',
            'slug' => 'danleo-hostel-(knust,-kumasi-campus)-32',
            'institution' => 'knust-kumasi-campus',
            'institution_name' => 'KNUST KUMASI CAMPUS',
            'location' => 'Ayeduasi - Ashanti Region',
            'distance' => '5 MINS Walk',
            'rating' => 1.0,
            'starting_price' => 3030,
            'male' => 7,
            'female' => 3,
            'unisex' => 0,
            'images' => [
                'b66QT4N6Enc2cOXhHuBcpOGrvtCLInOmdnOVwM74.jpg',
                'Oq1KIjDghci0YjgIH3FlcQaCEZzoTIsUpaudYxf6.jpg',
                'OejgZ04r2KbA24eOF0X3sPF0FxEPNAOBHRRxHpXq.jpg',
                'mXodqgPA69O1YzYsIVjbLBw2WqFOqcpwXflCukET.jpg',
                '1nyOjqWm1rX7iogyiXb6Yf4mRogxnqiTzQSG4d6S.jpg',
            ],
        ],
        [
            'name' => 'DESTINY VIEW HOSTEL (KNUST, KUMASI CAMPUS)',
            'slug' => 'destiny-view-hostel-(knust,-kumasi-campus)-40',
            'institution' => 'knust-kumasi-campus',
            'institution_name' => 'KNUST KUMASI CAMPUS',
            'location' => 'KNUST - Ashanti Region',
            'distance' => '8KM to Campus',
            'rating' => 4.0,
            'starting_price' => 3500,
            'male' => 24,
            'female' => 25,
            'unisex' => 9,
            'images' => [
                'mHGDX5UhGh8dQGapta6723xY6Q6Ukgra3RJRRS71.jpg',
                '9IW216KPRX9k8fFd50NX4fMrO25cnu1YB5DvQvfL.jpg',
            ],
        ],
        [
            'name' => 'DK APPIAH HOSTEL (KNUST, KUMASI CAMPUS)',
            'slug' => 'dk-appiah-hostel-(knust,-kumasi-campus)-44',
            'institution' => 'knust-kumasi-campus',
            'institution_name' => 'KNUST KUMASI CAMPUS',
            'location' => 'Boadi - Ashanti Region',
            'distance' => '10 Minutes Drive',
            'rating' => 5.0,
            'starting_price' => 3500,
            'male' => 1,
            'female' => 0,
            'unisex' => 30,
            'images' => [
                'TFZXswzajtRFPEncmI1QFQ6uuaBE8foj9SdCxGVs.jpg',
                'ZLqLxjnIhT5oaJCWGoW7WWj7EwoUD8RiecEIqyXd.jpg',
                '0vVb4Vgk6kG9tb1VNGJ913VlBptgkMXBGjcJi0NV.jpg',
            ],
        ],
        [
            'name' => 'EMMAL VILLE HOSTEL (KNUST, KUMASI CAMPUS)',
            'slug' => 'emmal-ville-hostel-(knust,-kumasi-campus)-35',
            'institution' => 'knust-kumasi-campus',
            'institution_name' => 'KNUST KUMASI CAMPUS',
            'location' => 'Ayeduasi - Ashanti Region',
            'distance' => '3 KM Walk to Campus',
            'rating' => 1.0,
            'starting_price' => 4000,
            'male' => 10,
            'female' => 5,
            'unisex' => 0,
            'images' => [
                'S8YvR1gKCc5Oj0uuXQmxzP8BAapqq2svXazfpXct.jpg',
                'pPi2YyL0hnMJtYhkQakQg0kk0TxTPuE5Cy2LfWys.jpg',
                'eTK4o5XZ4M6xiljpoK8i953INgubHsXgycRRRJWO.jpg',
                'MFwwldXjspiAGdb0DjN0lJsbBIccaCvM6X3CW8mh.jpg',
                'cy7JuBAb7ozjcYb67QEnyiYBP9wtkUPJNA5ImoiS.jpg',
            ],
        ],
        [
            'name' => 'EVANDY HOSTEL (KNUST, KUMASI CAMPUS)',
            'slug' => 'evandy-hostel-(knust,-kumasi-campus)-29',
            'institution' => 'knust-kumasi-campus',
            'institution_name' => 'KNUST KUMASI CAMPUS',
            'location' => 'Ayeduasi - Ashanti Region',
            'distance' => '5 MINS Walk',
            'rating' => 0.0,
            'starting_price' => 4600,
            'male' => 10,
            'female' => 10,
            'unisex' => 0,
            'images' => [
                'occSgqBctrjW84UPgnxozYTeEJ5xGQ7h14qpvcuH.jpg',
                '0VEPdIgYAFHnp5w7a7L462KqunO9AgelpuKDNvqZ.jpg',
                'YoCBJ4j8kTTJmxDfzFkXoposdUI9MtpUGEQooLkj.jpg',
                'PFnE4Q6HwWaEfdcOdL0lUsRh7kdoJYIXiE8XBYFq.jpg',
                '5Ocu6zY49WfLjg54xx8d9r0JaJUcgS4GmgdasEiZ.jpg',
                '8qRd06z80ewkJ20Yp6BoW5ywJFU2QMVPTeUCPOfY.jpg',
            ],
        ],
        [
            'name' => 'EZMIL HOSTEL (KNUST, KUMASI CAMPUS)',
            'slug' => 'ezmil-hostel-(knust,-kumasi-campus)-83',
            'institution' => 'knust-kumasi-campus',
            'institution_name' => 'KNUST KUMASI CAMPUS',
            'location' => 'Ayeduasi - Ashanti Region',
            'distance' => '3KM WALK TO CAMPUS',
            'rating' => 0.0,
            'starting_price' => 4000,
            'male' => 0,
            'female' => 0,
            'unisex' => 12,
            'images' => [
                'S9vNVDyBSYSxDt0ugy8wRXEXGmn1bmHJE0AWyhKK.png',
                't7MShoQHPanQXx8JlLWGQ4y4LKMrpQVf5v2eB0Dl.png',
            ],
        ],
        [
            'name' => 'GEORGIA HOSTEL (KNUST, KUMASI CAMPUS)',
            'slug' => 'georgia-hostel-(knust,-kumasi-campus)-47',
            'institution' => 'knust-kumasi-campus',
            'institution_name' => 'KNUST KUMASI CAMPUS',
            'location' => 'KNUST - Ashanti Region',
            'distance' => '3 Minutes Drive',
            'rating' => 0.0,
            'starting_price' => 6500,
            'male' => 11,
            'female' => 6,
            'unisex' => 0,
            'images' => [
                'Im31kADfnzE9aGlMKEJm90uX71UDKJupP0xvvva2.jpg',
                'wR4JT8wmlULKoZ2p04Jk46AkXcjg3rJHtTf7h73z.jpg',
                'TFmkSM1oLll1FGLKtI0asD7tXSgdcagcsBTuJ3xa.jpg',
                'ZIxIdYNLmK9haCnjKk7IqDnn37qqGCmKTAfjEABd.jpg',
                '2lEAUnbG7XDoVy8RgFhhsZxPXToNmdydnW4oM3r9.jpg',
                'qQUhA0O5oxfxUfl4OV8JR9rndZSDEol0UpWRF78q.jpg',
            ],
        ],
        [
            'name' => 'GHANA HOSTELS (KNUST, KUMASI CAMPUS)',
            'slug' => 'ghana-hostels-(knust,-kumasi-campus)-59',
            'institution' => 'knust-kumasi-campus',
            'institution_name' => 'KNUST KUMASI CAMPUS',
            'location' => 'KNUST - Ashanti Region',
            'distance' => '1.8KM',
            'rating' => 0.0,
            'starting_price' => 4200,
            'male' => 48,
            'female' => 54,
            'unisex' => 0,
            'images' => [
                'u42HxSzkBBamQ2ChN7Rz1mwDpTbMSakWFuyrtVg3.jpg',
                'b5McGzqEpQAInntKJKTE1H84LHfFGKWl0AlranOs.jpg',
                'svHAYE9DmuIyqWlKAsAnilQONBnd1QyhFt7ejJQO.jpg',
            ],
        ],
        [
            'name' => 'HONESTY HOSTEL (KNUST, KUMASI CAMPUS)',
            'slug' => 'honesty-hostel-(knust,-kumasi-campus)-43',
            'institution' => 'knust-kumasi-campus',
            'institution_name' => 'KNUST KUMASI CAMPUS',
            'location' => 'KNUST - Ashanti Region',
            'distance' => '200M FROM KNUST AHINSAN GATE',
            'rating' => 0.0,
            'starting_price' => 2500,
            'male' => 6,
            'female' => 2,
            'unisex' => 0,
            'images' => [
                '7Y13bdITnHX63X1itNve36enskrYpunSiAXuPf0m.jpg',
                'ucZadWalvBBDv27aiORhxS6orXBTKMY45zESijNL.jpg',
                'mH4gNXtgqM44q7oBvXfDy0EqJai6Ooyyfo8Fb1f4.jpg',
                'Ot7fzE83lXCOuLRgNiiYFK6S3xURpij2CHVUaF8L.jpg',
                '47hOrFKcD3M9oz6chrinLORjAGYKIrWGwqfeUI5r.jpg',
            ],
        ],
    ];

    public function run()
    {
        foreach ($this->hostels as $i => $data) {
            $seed = 5000 + $i;
            $coords = $this->coordinates[$data['slug']] ?? [null, null];
            $hostel = Hostel::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'institution' => $data['institution'],
                    'institution_name' => $data['institution_name'],
                    'location' => $data['location'],
                    'distance' => $data['distance'],
                    'latitude' => $coords[0],
                    'longitude' => $coords[1],
                    'rating' => $data['rating'],
                    'starting_price' => $data['starting_price'],
                    'description' => $this->description($data),
                    'is_active' => true,
                ]
            );

            foreach ($data['images'] as $pos => $img) {
                $hostel->images()->updateOrCreate(
                    ['image_path' => 'storage/thumbnails/' . $img],
                    ['position' => $pos]
                );
            }

            $hostel->rooms()->delete();
            $this->createRooms($hostel, $data, $seed);
        }
    }

    protected function createRooms(Hostel $hostel, array $data, int $seed)
    {
        mt_srand($seed);
        foreach (['Male' => 'male', 'Female' => 'female', 'Unisex' => 'unisex'] as $gender => $key) {
            $count = $data[$key];
            if ($count <= 0) {
                continue;
            }

            $slots = min(3, $count);
            $pool = array_keys($this->roomTypeMult);
            $types = ['Four in a room'];
            $pool = array_diff($pool, ['Four in a room']);
            $pool = array_values($pool);
            shuffle($pool);
            while (count($types) < $slots) {
                $types[] = array_pop($pool);
            }
            shuffle($types);

            $buckets = $this->distribute($count, $slots);

            $index = 0;
            foreach ($types as $type) {
                $mult = $this->roomTypeMult[$type];
                $price = $type === 'Four in a room'
                    ? $data['starting_price']
                    : (int) max($data['starting_price'], round($data['starting_price'] * $mult));

                $available = $buckets[$index] ?? 1;

                Room::create([
                    'hostel_id' => $hostel->id,
                    'name' => $this->roomName($hostel, $gender, $type),
                    'room_type' => $type,
                    'capacity' => $this->capacityFor($type),
                    'gender_category' => $gender,
                    'price' => $price,
                    'total_rooms' => $available,
                ]);
                $index++;
            }
        }
    }

    protected function capacityFor(string $type): int
    {
        return (int) (RoomCategory::where('name', $type)->value('capacity') ?? 1);
    }

    protected function distribute(int $total, int $slots): array
    {
        if ($slots <= 1) {
            return [$total];
        }
        $buckets = array_fill(0, $slots, 1);
        $left = $total - $slots;
        for ($i = 0; $i < $left; $i++) {
            $buckets[mt_rand(0, $slots - 1)]++;
        }
        return $buckets;
    }

    protected function roomName(Hostel $hostel, string $gender, string $type): string
    {
        return $gender . ' ' . $type;
    }

    protected function description(array $data): string
    {
        return $data['name'] . ' is a comfortable and secure student hostel located at ' . $data['location']
            . ', just ' . strtolower($data['distance']) . ' from campus. '
            . 'The hostel offers a range of room options to suit every budget and preference, '
            . 'with prices starting from GHS' . number_format($data['starting_price'], 2) . '. '
            . 'Facilities include 24/7 security, constant water and power supply, reliable internet '
            . 'connectivity, furnished rooms and a friendly environment for students. '
            . 'Book early to secure your space for the upcoming academic year.';
    }
}