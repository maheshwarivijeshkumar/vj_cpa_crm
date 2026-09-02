<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('languages')->count() > 0) {
            return;
        }

        $now = now();

        $languages = [
            // code, name, native_name, is_rtl
            ['af',    'Afrikaans',            'Afrikaans',          false],
            ['sq',    'Albanian',             'Shqip',              false],
            ['am',    'Amharic',              'አማርኛ',               false],
            ['ar',    'Arabic',               'العربية',            true],
            ['hy',    'Armenian',             'Հայերեն',            false],
            ['az',    'Azerbaijani',          'Azərbaycanca',       false],
            ['eu',    'Basque',               'Euskara',            false],
            ['be',    'Belarusian',           'Беларуская',         false],
            ['bn',    'Bengali',              'বাংলা',              false],
            ['bs',    'Bosnian',              'Bosanski',           false],
            ['bg',    'Bulgarian',            'Български',          false],
            ['ca',    'Catalan',              'Català',             false],
            ['ceb',   'Cebuano',              'Cebuano',            false],
            ['ny',    'Chichewa',             'Chichewa',           false],
            ['zh',    'Chinese (Simplified)', '中文(简体)',          false],
            ['zh-TW', 'Chinese (Traditional)','中文(繁體)',          false],
            ['co',    'Corsican',             'Corsu',              false],
            ['hr',    'Croatian',             'Hrvatski',           false],
            ['cs',    'Czech',                'Čeština',            false],
            ['da',    'Danish',               'Dansk',              false],
            ['nl',    'Dutch',                'Nederlands',         false],
            ['en',    'English',              'English',            false],
            ['eo',    'Esperanto',            'Esperanto',          false],
            ['et',    'Estonian',             'Eesti',              false],
            ['tl',    'Filipino',             'Filipino',           false],
            ['fi',    'Finnish',              'Suomi',              false],
            ['fr',    'French',               'Français',           false],
            ['fy',    'Frisian',              'Frysk',              false],
            ['gl',    'Galician',             'Galego',             false],
            ['ka',    'Georgian',             'ქართული',            false],
            ['de',    'German',               'Deutsch',            false],
            ['el',    'Greek',                'Ελληνικά',           false],
            ['gu',    'Gujarati',             'ગુજરાતી',           false],
            ['ht',    'Haitian Creole',       'Kreyòl Ayisyen',     false],
            ['ha',    'Hausa',                'Hausa',              false],
            ['haw',   'Hawaiian',             'ʻŌlelo Hawaiʻi',     false],
            ['he',    'Hebrew',               'עברית',              true],
            ['hi',    'Hindi',                'हिंदी',              false],
            ['hmn',   'Hmong',                'Hmoob',              false],
            ['hu',    'Hungarian',            'Magyar',             false],
            ['is',    'Icelandic',            'Íslenska',           false],
            ['ig',    'Igbo',                 'Igbo',               false],
            ['id',    'Indonesian',           'Bahasa Indonesia',   false],
            ['ga',    'Irish',                'Gaeilge',            false],
            ['it',    'Italian',              'Italiano',           false],
            ['ja',    'Japanese',             '日本語',              false],
            ['jv',    'Javanese',             'Basa Jawa',          false],
            ['kn',    'Kannada',              'ಕನ್ನಡ',              false],
            ['kk',    'Kazakh',               'Қазақша',            false],
            ['km',    'Khmer',                'ខ្មែរ',               false],
            ['rw',    'Kinyarwanda',          'Kinyarwanda',        false],
            ['ko',    'Korean',               '한국어',              false],
            ['ku',    'Kurdish (Kurmanji)',    'Kurdî',              false],
            ['ky',    'Kyrgyz',               'Кыргызча',           false],
            ['lo',    'Lao',                  'ລາວ',                false],
            ['la',    'Latin',                'Latina',             false],
            ['lv',    'Latvian',              'Latviešu',           false],
            ['lt',    'Lithuanian',           'Lietuvių',           false],
            ['lb',    'Luxembourgish',        'Lëtzebuergesch',     false],
            ['mk',    'Macedonian',           'Македонски',         false],
            ['mg',    'Malagasy',             'Malagasy',           false],
            ['ms',    'Malay',                'Bahasa Melayu',      false],
            ['ml',    'Malayalam',            'മലയാളം',             false],
            ['mt',    'Maltese',              'Malti',              false],
            ['mi',    'Maori',                'Te Reo Māori',       false],
            ['mr',    'Marathi',              'मराठी',              false],
            ['mn',    'Mongolian',            'Монгол',             false],
            ['my',    'Myanmar (Burmese)',     'မြန်မာဘာသာ',         false],
            ['ne',    'Nepali',               'नेपाली',             false],
            ['no',    'Norwegian',            'Norsk',              false],
            ['or',    'Odia (Oriya)',         'ଓଡ଼ିଆ',              false],
            ['ps',    'Pashto',               'پښتو',               true],
            ['fa',    'Persian',              'فارسی',              true],
            ['pl',    'Polish',               'Polski',             false],
            ['pt',    'Portuguese',           'Português',          false],
            ['pa',    'Punjabi',              'ਪੰਜਾਬੀ',             false],
            ['ro',    'Romanian',             'Română',             false],
            ['ru',    'Russian',              'Русский',            false],
            ['sm',    'Samoan',               'Gagana Samoa',       false],
            ['gd',    'Scots Gaelic',         'Gàidhlig',           false],
            ['sr',    'Serbian',              'Српски',             false],
            ['st',    'Sesotho',              'Sesotho',            false],
            ['sn',    'Shona',                'Shona',              false],
            ['sd',    'Sindhi',               'سنڌي',               true],
            ['si',    'Sinhala',              'සිංහල',              false],
            ['sk',    'Slovak',               'Slovenčina',         false],
            ['sl',    'Slovenian',            'Slovenščina',        false],
            ['so',    'Somali',               'Soomaali',           false],
            ['es',    'Spanish',              'Español',            false],
            ['su',    'Sundanese',            'Basa Sunda',         false],
            ['sw',    'Swahili',              'Kiswahili',          false],
            ['sv',    'Swedish',              'Svenska',            false],
            ['tg',    'Tajik',                'Тоҷикӣ',             false],
            ['ta',    'Tamil',                'தமிழ்',              false],
            ['tt',    'Tatar',                'Татар',              false],
            ['te',    'Telugu',               'తెలుగు',             false],
            ['th',    'Thai',                 'ภาษาไทย',            false],
            ['tr',    'Turkish',              'Türkçe',             false],
            ['tk',    'Turkmen',              'Türkmençe',          false],
            ['uk',    'Ukrainian',            'Українська',         false],
            ['ur',    'Urdu',                 'اردو',               true],
            ['ug',    'Uyghur',               'ئۇيغۇرچە',           true],
            ['uz',    'Uzbek',                'O\'zbek',            false],
            ['vi',    'Vietnamese',           'Tiếng Việt',         false],
            ['cy',    'Welsh',                'Cymraeg',            false],
            ['xh',    'Xhosa',               'isiXhosa',           false],
            ['yi',    'Yiddish',              'ייִדיש',              true],
            ['yo',    'Yoruba',               'Yorùbá',             false],
            ['zu',    'Zulu',                 'isiZulu',            false],
        ];

        $rows = [];
        foreach ($languages as [$code, $name, $nativeName, $isRtl]) {
            $rows[] = [
                'uuid'        => (string) Str::uuid(),
                'code'        => $code,
                'name'        => $name,
                'native_name' => $nativeName,
                'is_rtl'      => $isRtl,
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('languages')->insert($chunk);
        }
    }
}
