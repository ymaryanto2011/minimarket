<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    public function index()
    {
        abort_if(auth()->user()->role !== 'admin', 403);
        return view('setting.backup');
    }

    public function download()
    {
        abort_if(auth()->user()->role !== 'admin', 403);

        $dbName = config('database.connections.mysql.database');
        $tables  = DB::select('SHOW TABLES');
        $key     = 'Tables_in_' . $dbName;

        $sql  = "-- Minimarket POS - Database Backup\n";
        $sql .= "-- Generated  : " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database   : {$dbName}\n";
        $sql .= "-- Laravel App: " . config('app.name') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET NAMES utf8mb4;\n\n";

        foreach ($tables as $tableObj) {
            $table  = $tableObj->$key;
            $create = DB::select("SHOW CREATE TABLE `{$table}`");
            $createSql = $create[0]->{'Create Table'};

            $sql .= "-- ----------------------------\n";
            $sql .= "-- Table structure: `{$table}`\n";
            $sql .= "-- ----------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= "{$createSql};\n\n";

            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                $cols    = array_keys((array) $rows->first());
                $colList = '`' . implode('`, `', $cols) . '`';
                $sql .= "-- Data for: `{$table}` ({$rows->count()} rows)\n";
                $sql .= "INSERT INTO `{$table}` ({$colList}) VALUES\n";
                $chunks = $rows->chunk(500);
                $allValues = [];
                foreach ($rows as $row) {
                    $rowArr  = array_values((array) $row);
                    $escaped = array_map(
                        fn($v) => is_null($v) ? 'NULL' : "'" . addslashes((string) $v) . "'",
                        $rowArr
                    );
                    $allValues[] = '(' . implode(', ', $escaped) . ')';
                }
                $sql .= implode(",\n", $allValues) . ";\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $filename = 'backup_minimarket_' . now()->format('Ymd_His') . '.sql';

        return response($sql, 200, [
            'Content-Type'              => 'application/octet-stream',
            'Content-Disposition'       => "attachment; filename=\"{$filename}\"",
            'Content-Length'            => strlen($sql),
            'Cache-Control'             => 'no-store',
        ]);
    }
}
