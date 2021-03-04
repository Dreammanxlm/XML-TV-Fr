<?php
class Utils
{
    public static $CODE_CSA_TO_VALUE = [
        '10' => '-10',
        '12' => '-12',
        '16' => '-16',
        '18' => '-18',
        'TP' => 'Tout public'
    ];

    public static function generateFilePath($xmlpath, $channel, $date)
    {
        return $xmlpath . $channel . '_' . $date . '.xml';
    }

    public static function buildNumEpisode($p)
    {
        $num = '';

        if ($p['season_number']) {
            $num .= $p['season_number'] - 1;
            if ($p['total_seasons']) {
                $num .= '/' . $p['total_seasons'];
            }
        }

        $num .= '.';

        if ($p['episode_number']) {
            $num .= $p['episode_number'] - 1;
            if ($p['total_episodes']) {
                $num .= '/' . $p['total_episodes'];
            }
        }

        $num .= '.';

        return $num != '..' ? $num : '';
    }

    public static function buildProgramDescription($p)
    {
        $desc = '';

        if (isset($p['series'])) {
            $episode_infos = [];
            if ($p['series']['season_number']) {
                $episode_infos[] = 'Saison ' . $p['series']['season_number'];
            }
            if ($p['series']['episode_number']) {
                $episode_infos[] = 'Episode ' . $p['series']['episode_number'];
            }
            if (!empty($episode_infos)) {
                $desc .= implode(' ', $episode_infos);
            }
        }

        if ($p['summary']) {
            if ($desc != '') {
                $desc .= chr(10);
            }
            $desc .= $p['summary'];
        }

        if ($p['critical']) {
            $desc .= chr(10) . 'Critique : ' . $p['critical'];
        }

        if ($p['year']) {
            $desc .= chr(10) . 'Année de réalisation : ' . $p['year'];
        }

        if ($p['original_title']) {
            $desc .= chr(10) . 'Titre original : ' . $p['original_title'];
        }

        $intervenants = $p['intervenants'] ? $p['intervenants'] : [];
        foreach ($intervenants as $role => $int) {
            $desc .= chr(10) . $role . ' : ' . $int;
        }

        $desc = strip_tags($desc);

        return $desc;
    }

    public static function buildFileContent($programs)
    {
        $xml = '';

        foreach ($programs as $p) {
            $xml .= self::buildProgramXML($p);
            $xml .= chr(10);
        }

        return $xml;
    }

    public static function buildProgramXML($p)
    {
        $str = '  <programme start="'   . $p['start']
                        . '" stop="'    . $p['stop']
                        . '" channel="' . $p['channel'] . '">';

        $titles = isset($p['titles']) ? $p['titles'] : [];
        foreach ($titles as $t) {
            $str .= chr(10) . '    <title lang="' . $t['lang'] . '">' . htmlspecialchars($t['title'], ENT_XML1) . '</title>';
        }

        $descriptions = isset($p['descriptions']) ? $p['descriptions'] : [];
        foreach ($descriptions as $d) {
            $str .= chr(10) . '    <desc lang="' . $d['lang'] . '">' . htmlspecialchars($d['description'], ENT_XML1) . '</desc>';
        }

        if ($p['date']) {
            $str .= chr(10) . '    <date>' . $p['date'] . '</date>';
        }

        $countries = isset($p['countries']) ? $p['countries'] : [];
        foreach ($countries as $c) {
            $str .= chr(10) . '    <country lang="' . $c['lang'] . '">' . htmlspecialchars($c['country'], ENT_XML1) . '</country>';
        }

        $categories = isset($p['categories']) ? $p['categories'] : [];
        foreach ($categories as $c) {
            $str .= chr(10) . '    <category lang="' . $c['lang'] . '">' . htmlspecialchars($c['category'], ENT_XML1) . '</category>';
        }

        if (isset($p['episode'])) {
            $str .= chr(10) . '    <episode-num system="' . $p['episode']['system'] . '">' . $p['episode']['num'] . '</episode-num>';
        }

        $subtitles = isset($p['subtitles']) ? $p['subtitles'] : [];
        foreach ($subtitles as $s) {
            $str .= chr(10) . '    <sub-title lang="' . $s['lang'] .'">' . htmlspecialchars($s['subtitle'], ENT_XML1) . '</sub-title>';
        }

        $icons = isset($p['icons']) ? $p['icons'] : [];
        foreach ($icons as $icon) {
            $str .= chr(10) . '    <icon src="' . htmlspecialchars($icon['src'], ENT_XML1) . '" />';
        }

        if (isset($p['intervenants'])) {
            $str .= chr(10) . '    <credits>';
            foreach ($p['intervenants'] as $role => $int) {
                $str .= chr(10) . '      <' . $role . '>' . htmlspecialchars($int, ENT_XML1) . '</' . $role . '>';
            }
            $str .= chr(10) . '    </credits>';
        }

        $ratings = isset($p['ratings']) ? $p['ratings'] : [];
        foreach ($ratings as $rating) {
            $str .= chr(10) . '    <rating system="' . $rating['system'] . '">'
                  . chr(10) . '      <value>' . $rating['value'] . '</value>'
                  . chr(10) . '    </rating>';
        }

        if (isset($p['star_rating'])) {
            $str .= chr(10) . '    <star-rating>'
                  . chr(10) . '      <value>' . $p['star_rating']['value'] . '</value>'
                  . chr(10) . '    </star-rating>';
        }

        $str .= chr(10) . '  </programme>';

        $str = str_replace("\0", '', $str);

        return $str;
    }
}