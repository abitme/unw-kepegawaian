
DROP TABLE IF EXISTS groups_access_menu;
CREATE OR REPLACE VIEW groups_access_menu AS
SELECT  menus.id, menus.label, menus.link, menus.parent, menus.icon, menus.sort, groups_access.group_id, groups_access.menu_id
FROM groups_access
LEFT JOIN menus ON groups_access.menu_id = menus.id;

DROP TABLE IF EXISTS view_pertanyaan;
CREATE OR REPLACE VIEW view_pertanyaan AS 
SELECT pertanyaan.*, pertanyaan_kategori.kategori, pertanyaan_kategori.nilai_min, pertanyaan_kategori.nilai_max, pertanyaan_kategori.range_desc
FROM pertanyaan
LEFT JOIN pertanyaan_kategori ON pertanyaan_kategori.id = pertanyaan.id_pertanyaan_kategori;

DROP TABLE IF EXISTS view_penilaian;
CREATE OR REPLACE VIEW view_penilaian AS
SELECT penilaian.*, pegawai_penilai.nama AS nama_penilai, pegawai_dinilai.nama AS nama_dinilai
FROM penilaian 
LEFT JOIN pegawai pegawai_penilai ON pegawai_penilai.id = penilaian.id_pegawai_penilai
LEFT JOIN pegawai pegawai_dinilai ON pegawai_dinilai.id = penilaian.id_pegawai_dinilai;

DROP TABLE IF EXISTS view_penilaian_detail;
CREATE OR REPLACE VIEW view_penilaian_detail AS
SELECT penilaian_detail.*, view_pertanyaan.pertanyaan, view_pertanyaan.id_pertanyaan_kategori, view_pertanyaan.kategori, view_pertanyaan.nilai_min, view_pertanyaan.nilai_max, view_pertanyaan.range_desc
FROM penilaian_detail
LEFT JOIN view_pertanyaan ON view_pertanyaan.id = penilaian_detail.id_pertanyaan;

DROP TABLE IF EXISTS setting_penilaian_detail_view;
DROP VIEW IF EXISTS setting_penilaian_detail_view;
CREATE OR REPLACE VIEW setting_penilaian_detail_view AS
SELECT setting_penilaian_detail.*, view_pertanyaan.pertanyaan, view_pertanyaan.kategori, view_pertanyaan.nilai_min, view_pertanyaan.nilai_max, view_pertanyaan.range_desc
FROM setting_penilaian_detail
LEFT JOIN view_pertanyaan ON view_pertanyaan.id = setting_penilaian_detail.id_pertanyaan;

DROP TABLE IF EXISTS jabatan_u_view;
CREATE OR REPLACE VIEW jabatan_u_view AS
SELECT jabatan_u.id, jabatan_u.id_unit, unit.nama_unit, unit.is_separated, jabatan_u.id_jabatan, jabatan.nama_jabatan
FROM jabatan_u
LEFT JOIN jabatan ON jabatan.id = jabatan_u.id_jabatan
LEFT JOIN unit ON unit.id = jabatan_u.id_unit;

DROP TABLE IF EXISTS jabatan_struktural_u_view;
CREATE OR REPLACE VIEW jabatan_struktural_u_view AS
SELECT jabatan_struktural_u.id, jabatan_struktural_u.id_unit, unit.nama_unit, jabatan_struktural_u.id_jabatan_struktural, jabatan_struktural.nama_jabatan_struktural
FROM jabatan_struktural_u
LEFT JOIN jabatan_struktural ON jabatan_struktural.id = jabatan_struktural_u.id_jabatan_struktural
LEFT JOIN unit ON unit.id = jabatan_struktural_u.id_unit;

DROP TABLE IF EXISTS jabatan_fungsional_view;
CREATE OR REPLACE VIEW jabatan_fungsional_view AS
SELECT jabatan_fungsional.id, jabatan_fungsional.id_jabatan, jabatan.nama_jabatan, jabatan_fungsional.nama_jabatan_fungsional
FROM jabatan_fungsional
LEFT JOIN jabatan ON jabatan.id = jabatan_fungsional.id_jabatan;

DROP TABLE IF EXISTS pegawai_jabatan_u_view;
CREATE OR REPLACE VIEW pegawai_jabatan_u_view AS
SELECT pegawai_jabatan_u.id, pegawai_jabatan_u.id_pegawai, pegawai.nama, pegawai_jabatan_u.id_jabatan_u, jabatan_u_view.id_unit, jabatan_u_view.nama_unit, jabatan_u_view.is_separated, jabatan_u_view.id_jabatan, jabatan_u_view.nama_jabatan, pegawai_jabatan_u.tmt
FROM pegawai_jabatan_u
LEFT JOIN pegawai ON pegawai.id = pegawai_jabatan_u.id_pegawai
LEFT JOIN jabatan_u_view ON jabatan_u_view.id = pegawai_jabatan_u.id_jabatan_u;

DROP TABLE IF EXISTS pegawai_jabatan_struktural_u_view;
CREATE OR REPLACE VIEW pegawai_jabatan_struktural_u_view AS
SELECT pegawai_jabatan_struktural_u.id, pegawai_jabatan_struktural_u.id_pegawai, pegawai.nama, pegawai_jabatan_struktural_u.id_jabatan_struktural_u, jabatan_struktural_u_view.id_unit, jabatan_struktural_u_view.nama_unit, jabatan_struktural_u_view.id_jabatan_struktural, jabatan_struktural_u_view.nama_jabatan_struktural, pegawai_jabatan_struktural_u.tanggal_mulai, pegawai_jabatan_struktural_u.tanggal_selesai
FROM pegawai_jabatan_struktural_u
LEFT JOIN pegawai ON pegawai.id = pegawai_jabatan_struktural_u.id_pegawai
LEFT JOIN jabatan_struktural_u_view ON jabatan_struktural_u_view.id = pegawai_jabatan_struktural_u.id_jabatan_struktural_u;

DROP TABLE IF EXISTS pegawai_jabatan_fungsional_view;
CREATE OR REPLACE VIEW pegawai_jabatan_fungsional_view AS
SELECT pegawai_jabatan_fungsional.id, pegawai_jabatan_fungsional.id_pegawai, pegawai.nama, pegawai_jabatan_fungsional.id_jabatan_fungsional, jabatan_fungsional_view.nama_jabatan, jabatan_fungsional_view.nama_jabatan_fungsional
FROM pegawai_jabatan_fungsional
LEFT JOIN pegawai ON pegawai.id = pegawai_jabatan_fungsional.id_pegawai
LEFT JOIN jabatan_fungsional_view ON jabatan_fungsional_view.id = pegawai_jabatan_fungsional.id_jabatan_fungsional;

DROP TABLE IF EXISTS jadwal_kerja_detail_view;
CREATE OR REPLACE VIEW jadwal_kerja_detail_view AS
SELECT jadwal_kerja_detail.id, jadwal_kerja_detail.id_jadwal_kerja, jadwal_kerja_detail.id_jam_kerja,jam_kerja.jam_masuk, jam_kerja.jam_istirahat_mulai, jam_kerja.jam_istirahat_selesai, jam_kerja.jam_pulang,  jadwal_kerja_detail.day, jadwal_kerja_detail.libur
FROM jadwal_kerja_detail 
LEFT JOIN jam_kerja ON jam_kerja.id = jadwal_kerja_detail.id_jam_kerja;

DROP TABLE IF EXISTS jadwal_kerja_auto_detail_view;
CREATE OR REPLACE VIEW jadwal_kerja_auto_detail_view AS
SELECT jadwal_kerja_auto_detail.*, jam_kerja.nama_jam_kerja, jam_kerja.jam_masuk, jam_kerja.jam_istirahat_mulai, jam_kerja.jam_istirahat_selesai, jam_kerja.jam_pulang, jam_kerja.is_diffday
FROM jadwal_kerja_auto_detail
LEFT JOIN jam_kerja ON jam_kerja.id = jadwal_kerja_auto_detail.id_jam_kerja;

DROP TABLE IF EXISTS jadwal_pegawai_view;
CREATE OR REPLACE VIEW jadwal_pegawai_view AS
SELECT jadwal_pegawai.id, jadwal_pegawai.id_jadwal_kerja, jadwal_kerja.nama_jadwal_kerja, jadwal_pegawai.id_pegawai, pegawai.nama
FROM jadwal_pegawai
LEFT JOIN jadwal_kerja ON jadwal_kerja.id = jadwal_pegawai.id_jadwal_kerja
LEFT JOIN pegawai ON pegawai.id = jadwal_pegawai.id_pegawai;

DROP TABLE IF EXISTS jadwal_auto_pegawai_view;
CREATE OR REPLACE VIEW jadwal_auto_pegawai_view AS
SELECT jadwal_auto_pegawai.id, jadwal_auto_pegawai.id_jadwal_kerja_auto, jadwal_kerja_auto.nama_jadwal_kerja, jadwal_auto_pegawai.id_pegawai, pegawai.nama
FROM jadwal_auto_pegawai
LEFT JOIN jadwal_kerja_auto ON jadwal_kerja_auto.id = jadwal_auto_pegawai.id_jadwal_kerja_auto
LEFT JOIN pegawai ON pegawai.id = jadwal_auto_pegawai.id_pegawai;

DROP TABLE IF EXISTS presensi_view;
CREATE OR REPLACE VIEW presensi_view AS
SELECT presensi.id, presensi.id_pegawai, pegawai.nik, pegawai.nama, presensi.tipe, presensi.photo, presensi.coord_latitude, presensi.coord_longitude, presensi.jam_masuk_pulang, presensi.jam_istirahat, presensi.waktu, presensi.validitas, presensi.alasan_pulang_cepat, presensi.diffday_code
FROM presensi
LEFT JOIN pegawai ON pegawai.id = presensi.id_pegawai;

DROP TABLE IF EXISTS presensi_piket_view;
CREATE OR REPLACE VIEW presensi_piket_view AS 
SELECT presensi_piket.*, pegawai.nama
FROM presensi_piket
LEFT JOIN pegawai ON pegawai.id = presensi_piket.id_pegawai;

DROP TABLE IF EXISTS presensi_acara_view;
CREATE OR REPLACE VIEW presensi_acara_view AS 
SELECT presensi_acara.*, acara.nama_acara, acara.barcode, acara.tanggal
FROM presensi_acara
LEFT JOIN acara ON acara.id = presensi_acara.id_acara;

DROP TABLE IF EXISTS absensi_view;
CREATE OR REPLACE VIEW absensi_view AS
SELECT absensi.id, absensi.id_pegawai, absensi.kode_absensi, absensi.jenis_absensi, absensi.keterangan, min(absensi.tanggal) as tanggal_awal, max(absensi.tanggal) AS tanggal_akhir, pegawai.nama
FROM absensi
LEFT JOIN pegawai ON pegawai.id = absensi.id_pegawai
GROUP BY kode_absensi;

DROP TABLE IF EXISTS hari_libur_view;
CREATE OR REPLACE VIEW hari_libur_view AS
SELECT hari_libur.id, hari_libur.kode_unik, hari_libur.keterangan, min(hari_libur.tanggal) as tanggal_awal, max(hari_libur.tanggal) AS tanggal_akhir, hari_libur.id_unit_piket
FROM hari_libur
GROUP BY kode_unik;

DROP TABLE IF EXISTS tugas_dinas_view;
CREATE OR REPLACE VIEW tugas_dinas_view AS
SELECT tugas_dinas.id, tugas_dinas.id_user, tugas_dinas.id_pegawai, tugas_dinas.kode_tugas_dinas, tugas_dinas.lumsum, tugas_dinas.keterangan, min(tugas_dinas.tanggal) as tanggal_awal, max(tugas_dinas.tanggal) AS tanggal_akhir, pegawai.nama, tugas_dinas.created_at
FROM tugas_dinas
LEFT JOIN pegawai ON pegawai.id = tugas_dinas.id_pegawai
GROUP BY kode_tugas_dinas;

DROP TABLE IF EXISTS pegawai_list_view;
CREATE OR REPLACE VIEW pegawai_list_view AS
SELECT pegawai.*, concat((SELECT COALESCE(GROUP_CONCAT(CONCAT_WS(' ', nama_jabatan, nama_unit)), '') FROM pegawai_jabatan_u_view WHERE id_pegawai = pegawai.id),", ",(SELECT COALESCE(GROUP_CONCAT(CONCAT_WS(' ', nama_jabatan_struktural, nama_unit)), '') FROM pegawai_jabatan_struktural_u_view WHERE id_pegawai = pegawai.id),", ",(SELECT COALESCE(GROUP_CONCAT(CONCAT_WS(' ', nama_jabatan, nama_jabatan_fungsional)), '') FROM pegawai_jabatan_fungsional_view WHERE id_pegawai = pegawai.id),"") AS jabatan
FROM pegawai;

DROP TABLE IF EXISTS pegawai_unit_view;
CREATE OR REPLACE VIEW pegawai_unit_view AS
SELECT id_pegawai as id, nama, id_unit FROM pegawai_jabatan_u_view
UNION
SELECT id_pegawai as id, nama, id_unit FROM pegawai_jabatan_struktural_u_view;

DROP TABLE IF EXISTS presensi_lupa_view;
CREATE OR REPLACE VIEW presensi_lupa_view AS
SELECT presensi_lupa.*, pegawai.nama
FROM presensi_lupa
LEFT JOIN pegawai ON pegawai.id = presensi_lupa.id_pegawai;

DROP TABLE IF EXISTS presensi_izin_view;
CREATE OR REPLACE VIEW presensi_izin_view AS
SELECT presensi_izin.*, pegawai.nama
FROM presensi_izin
LEFT JOIN pegawai ON pegawai.id = presensi_izin.id_pegawai;

DROP TABLE IF EXISTS unit_piket_view;
CREATE OR REPLACE VIEW unit_piket_view AS 
SELECT  unit_piket.id, unit_piket.id_unit, unit.nama_unit, unit.is_active, unit_piket.day
FROM unit_piket
LEFT JOIN unit ON unit.id = unit_piket.id_unit;

DROP TABLE IF EXISTS view_verifikasi_form_presensi;
CREATE OR REPLACE VIEW 	view_verifikasi_form_presensi AS
SELECT verifikasi_form_presensi.id, verifikasi_form_presensi.id_pegawai, p1.nik as nik_pegawai, p1.nama AS nama_pegawai, verifikasi_form_presensi.id_pegawai_verifikasi, p2.nik as nik_pegawai_verifikasi, p2.nama AS nama_pegawai_verifikasi
FROM verifikasi_form_presensi
LEFT JOIN pegawai p1 ON p1.id = verifikasi_form_presensi.id_pegawai
LEFT JOIN pegawai p2 ON p2.id = verifikasi_form_presensi.id_pegawai_verifikasi;


-- hotfix
-- https://stackoverflow.com/questions/1202919/mysql-dayofweek-my-week-begins-with-monday
-- https://www.anycodings.com/1questions/536804/mysql-timediff-negative-value
DROP TABLE IF EXISTS view_rekap_presensi;
CREATE OR REPLACE VIEW view_rekap_presensi AS
SELECT presensi.id_pegawai, presensi.jam_masuk_pulang as jadwal_jam_masuk_pulang, 
TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1))) AS jadwal_timediff, 
presensi.jam_istirahat as jadwal_jam_istirahat, 
TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_istirahat, ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_istirahat, ' - ', 1), ' - ', -1))) AS jadwal_timediff_istirahat, 
GROUP_CONCAT(time(waktu) ORDER BY tipe ASC SEPARATOR ' - ') AS jam_masuk_pulang, 
GROUP_CONCAT(alasan_pulang_cepat ORDER BY tipe ASC SEPARATOR '') AS alasan_pulang_cepat, 
TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 1), ' - ', -1))) AS jam_timediff, 
date(waktu) as tanggal, WEEKDAY(date(waktu)) + 1 as day, diffday_code
FROM `presensi` 
WHERE  (presensi.id_pegawai, DATE(presensi.waktu)) NOT IN
(
    SELECT * FROM
    (
        SELECT tugas_dinas.id_pegawai, tugas_dinas.tanggal
        FROM tugas_dinas
        LEFT JOIN presensi ON presensi.id_pegawai = tugas_dinas.id_pegawai
        WHERE tugas_dinas.lumsum = 1
    ) AS subquery
)
AND diffday_code = ''
GROUP BY presensi.id_pegawai, tanggal
UNION
SELECT presensi.id_pegawai, presensi.jam_masuk_pulang as jadwal_jam_masuk_pulang, 
CASE
    WHEN TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1))) > 0 THEN TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1)))
    ELSE TIME(TIMEDIFF(CONCAT('1999-09-09 ', time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1))), CONCAT('1999-09-08 ', time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1))) ) )
END AS jadwal_timediff,
presensi.jam_istirahat as jadwal_jam_istirahat, 
TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_istirahat, ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_istirahat, ' - ', 1), ' - ', -1))) AS jadwal_timediff_istirahat, 
GROUP_CONCAT(time(waktu) ORDER BY tipe ASC SEPARATOR ' - ') AS jam_masuk_pulang, 
GROUP_CONCAT(alasan_pulang_cepat ORDER BY tipe ASC SEPARATOR '') AS alasan_pulang_cepat, 
CASE
    WHEN TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 1), ' - ', -1))) > 0 THEN TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 1), ' - ', -1)))
    WHEN time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 1), ' - ', -1)) = time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 2), ' - ', -1)) THEN "00:00:00.000000"
    ELSE TIME(TIMEDIFF(CONCAT('1999-09-09 ', time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 2), ' - ', -1))), CONCAT('1999-09-08 ', time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 1), ' - ', -1))) ))
END  AS jam_timediff, 
date(waktu) as tanggal, WEEKDAY(date(waktu)) + 1 as day, diffday_code
FROM `presensi` 
WHERE  (presensi.id_pegawai, DATE(presensi.waktu)) NOT IN
(
    SELECT * FROM
    (
        SELECT tugas_dinas.id_pegawai, tugas_dinas.tanggal
        FROM tugas_dinas
        LEFT JOIN presensi ON presensi.id_pegawai = tugas_dinas.id_pegawai
        WHERE tugas_dinas.lumsum = 1
    ) AS subquery
)
AND diffday_code != ''
GROUP BY presensi.id_pegawai, diffday_code;

DROP TABLE IF EXISTS view_rekap_presensi_lupa;
CREATE OR REPLACE VIEW view_rekap_presensi_lupa AS
SELECT presensi_lupa.id_pegawai, presensi_lupa.jadwal_jam_masuk_pulang, 
TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(jadwal_jam_masuk_pulang, ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(jadwal_jam_masuk_pulang, ' - ', 1), ' - ', -1))) AS jadwal_timediff, 
CONCAT_WS(' - ', jam_masuk, jam_pulang) AS jam_masuk_pulang, 
TIMEDIFF(time(jam_pulang), time(jam_masuk)) AS jam_timediff, 
tanggal, WEEKDAY(tanggal) + 1 as day
FROM `presensi_lupa` 
WHERE  (presensi_lupa.id_pegawai, tanggal) NOT IN
(
    SELECT * FROM
    (
        SELECT tugas_dinas.id_pegawai, tugas_dinas.tanggal
        FROM tugas_dinas
        LEFT JOIN presensi_lupa ON presensi_lupa.id_pegawai = tugas_dinas.id_pegawai
    ) AS subquery
) AND status = 'Diterima'
GROUP BY presensi_lupa.id_pegawai, tanggal;

DROP TABLE IF EXISTS view_rekap_presensi_izin;
CREATE OR REPLACE VIEW view_rekap_presensi_izin AS
SELECT presensi_izin.id_pegawai, presensi_izin.jadwal_jam_masuk_pulang, 
TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(jadwal_jam_masuk_pulang, ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(jadwal_jam_masuk_pulang, ' - ', 1), ' - ', -1))) AS jadwal_timediff, 
CONCAT_WS(' - ', jam_masuk, jam_pulang) AS jam_masuk_pulang, 
TIMEDIFF(time(jam_pulang), time(jam_masuk)) AS jam_timediff, 
tanggal, WEEKDAY(tanggal) + 1 as day
FROM `presensi_izin` 
WHERE  (presensi_izin.id_pegawai, tanggal) NOT IN
(
    SELECT * FROM
    (
        SELECT tugas_dinas.id_pegawai, tugas_dinas.tanggal
        FROM tugas_dinas
        LEFT JOIN presensi_izin ON presensi_izin.id_pegawai = tugas_dinas.id_pegawai
    ) AS subquery
) AND status2 = 'Diterima'
GROUP BY presensi_izin.id_pegawai, tanggal;

DROP TABLE IF EXISTS view_rekap_presensi_piket;
CREATE OR REPLACE VIEW view_rekap_presensi_piket AS
SELECT presensi_piket.id_pegawai,
GROUP_CONCAT(time(waktu) ORDER BY tipe ASC SEPARATOR ' - ') AS jam_masuk_pulang, 
date(waktu) as tanggal, WEEKDAY(date(waktu)) + 1 as day
FROM `presensi_piket` 
WHERE  (presensi_piket.id_pegawai, DATE(presensi_piket.waktu)) NOT IN
(
    SELECT * FROM
    (
        SELECT tugas_dinas.id_pegawai, tugas_dinas.tanggal
        FROM tugas_dinas
        LEFT JOIN presensi_piket ON presensi_piket.id_pegawai = tugas_dinas.id_pegawai
    ) AS subquery
)
GROUP BY presensi_piket.id_pegawai, tanggal;

DROP TABLE IF EXISTS view_unit_relations;
CREATE OR REPLACE VIEW view_unit_relations AS
SELECT unit_relations.id, uparent.id AS parent, uparent.nama_unit AS parent_name, uchild.id AS child, uchild.nama_unit AS child_name, uchild.is_assess AS is_child_assess, unit_relations.depth
FROM `unit_relations` 
LEFT JOIN unit uparent on uparent.id = unit_relations.parent
LEFT JOIN unit uchild on uchild.id = unit_relations.child
LEFT JOIN unit on unit.id = uchild.id
ORDER BY `parent`,`depth`,`child_name` ASC;
