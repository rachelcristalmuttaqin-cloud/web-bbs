<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Approximate the closest numbered BBS checklist item from the source category
 * and observation text when the source data has no explicit checklist number.
 */
class BbsChecklistMapper
{
    public static function code(string $category, string $description): string
    {
        $category = Str::lower($category);
        $text = Str::lower($category.' '.$description);

        if (self::has($category, ['apd', 'alat pelindung diri'])) {
            return match (true) {
                self::has($text, ['mata', 'wajah', 'kacamata', 'glasses', 'face shield']) => '8.2',
                self::has($text, ['telinga', 'pendengaran', 'earplug', 'ear muff']) => '8.3',
                self::has($text, ['respirator', 'masker', 'masker', 'pernafasan']) => '8.4',
                self::has($text, ['sarung tangan', 'gloves', 'hand protection']) => '8.5',
                self::has($text, ['harness', 'pelindung jatuh', 'fall protection']) => '8.6',
                self::has($text, ['wearpack', 'pakaian pelindung', 'coverall']) => '8.7',
                self::has($text, ['sepatu', 'shoes', 'pelindung kaki']) => '8.8',
                default => '8.1',
            };
        }

        if (self::has($text, ['loto', 'lockout', 'lock-out', 'tag-out', 'isolasi energi'])) {
            return '4.3';
        }

        if (self::has($category, ['b3', 'berbahaya dan beracun'])) {
            return match (true) {
                self::has($text, ['tumpahan', 'spill', 'mencegah ceceran']) => '7.1',
                self::has($text, ['membersihkan', 'pembersihan', 'clean up', 'clean-up']) => '7.2',
                default => '7.3',
            };
        }

        if (self::has($category, ['mengemudi', 'transportasi'])) {
            return match (true) {
                self::has($text, ['mundur', 'memundurkan', 'revers', 'backing']) => '9.8',
                self::has($text, ['kecepatan', 'speed']) => '9.3',
                self::has($text, ['jarak', 'distance']) => '9.4',
                self::has($text, ['sabuk', 'seat belt', 'seatbelt']) => '9.2',
                default => '9.1',
            };
        }

        if (self::has($category, ['housekeeping', 'kebersihan area'])) {
            return match (true) {
                self::has($text, ['cahaya', 'penerangan', 'lighting']) => '5.3',
                self::has($text, ['posisi stabil', 'stabil']) => '5.1',
                default => '5.2',
            };
        }

        if (self::has($category, ['posisi', 'ergonomi'])) {
            return match (true) {
                self::has($text, ['line of fire', 'lintasan bahaya']) => '1.1',
                self::has($text, ['berjalan', 'bergerak', 'moving']) => '1.2',
                self::has($text, ['pandangan', 'penglihatan', 'mata pada pekerjaan']) => '1.3',
                self::has($text, ['terjepit', 'pinch point', 'jepit']) => '1.4',
                self::has($text, ['tangga', 'ladder', 'stair']) => '1.5',
                self::has($text, ['mengangkat', 'menurunkan', 'menekan', 'menarik', 'lifting']) => '2.1',
                self::has($text, ['memuntir', 'memutar badan', 'twisting']) => '2.2',
                self::has($text, ['menggapai', 'meraih', 'menjangkau', 'reach']) => '2.3',
                self::has($text, ['punggung', 'back support']) => '6.4',
                self::has($text, ['pundak', 'bahu', 'shoulder']) => '6.5',
                self::has($text, ['pergelangan', 'wrist', 'tangan']) => '6.6',
                self::has($text, ['mouse']) => '6.7',
                self::has($text, ['istirahat berkala', 'break']) => '6.1',
                self::has($text, ['ergonomi', 'ergonomic']) => '2.4',
                default => '1.1',
            };
        }

        if (self::has($category, ['perkakas', 'peralatan kerja'])) {
            return self::has($text, ['barikade', 'barrier', 'pelindung', 'rambu', 'peringatan', 'guard'])
                ? '3.2'
                : '3.1';
        }

        if (self::has($category, ['prosedur', 'izin kerja', 'permit'])) {
            return match (true) {
                self::has($text, ['hot work', 'hot-work', 'pekerjaan panas']) => '4.4',
                self::has($text, ['confined space', 'ruang terbatas']) => '4.5',
                self::has($text, ['komunikasi', 'communication']) => '4.7',
                self::has($text, ['jsa', 'job safety analysis', 'persiapan kerja']) => '4.1',
                self::has($text, ['sop', 'prosedur', 'permit', 'izin kerja', 'work permit']) => '4.2',
                default => '4.1',
            };
        }

        if (self::has($category, ['listrik', 'mekanikal'])) {
            return self::has($text, ['barikade', 'barrier', 'guard', 'rambu']) ? '3.2' : '3.1';
        }

        if (self::has($category, ['darurat', 'kesiapsiagaan'])) {
            return '10.1';
        }

        return '10.1';
    }

    private static function has(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
