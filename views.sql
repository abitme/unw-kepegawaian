SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));


-- trigger insert for unit
DELIMITER $$
CREATE TRIGGER insert_unit_relations 
    AFTER INSERT 
    ON unit
    FOR EACH ROW 
BEGIN
    INSERT INTO unit_relations
    (parent, child, depth)
    VALUES
    (NEW.id, NEW.id, 0);
END$$

-- trigger delete for unit
DELIMITER $$
CREATE TRIGGER delete_unit_relations 
    AFTER DELETE 
    ON unit
    FOR EACH ROW 
BEGIN
  delete link
  from unit_relations p, unit_relations link, unit_relations c, unit_relations to_delete
 where p.parent = link.parent      and c.child = link.child
   and p.child  = to_delete.parent and c.parent= to_delete.child
   and (to_delete.parent=OLD.id or to_delete.child=OLD.id)
   and to_delete.depth<2;
END$$

-- https://stackoverflow.com/questions/64935069/mysql-trigger-using-value-from-another-table
-- trigger insert jadwal_pegawai
DELIMITER $$
CREATE TRIGGER insert_jadwal_pegawai 
    AFTER INSERT 
    ON pegawai
    FOR EACH ROW 
BEGIN
    INSERT INTO jadwal_pegawai (id_pegawai, id_jadwal_kerja)
    SELECT new.id, j.id
    FROM jadwal_kerja j
    WHERE is_default = 1;
END$$

--     SELECT new.qty, new.quantity, new.real_price, p.average_price
--     FROM produk p

-- -- insert unit link (relations)
-- insert into unit_relations(parent, child, depth)
-- select p.parent, c.child, p.depth+c.depth+1
-- from unit_relations p, unit_relations c
-- where p.child=1 and c.parent=2

-- -- delete unit link (relations)
-- delete link
-- from unit_relations p, unit_relations link, unit_relations c
-- where p.parent = link.parent and c.child = link.child
-- and p.child=1 and c.parent=3

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



-- https://stackoverflow.com/questions/2696884/split-value-from-one-field-to-two
-- https://stackoverflow.com/questions/34992575/mysql-substring-extraction-using-delimiter
-- DROP view view_rekap_presensi;
-- CREATE OR REPLACE VIEW view_rekap_presensi AS
-- SELECT presensi.id_pegawai, presensi.jam_masuk_pulang as jadwal_jam_masuk_pulang, 
-- TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1))) AS jadwal_timediff, 
-- GROUP_CONCAT(time(waktu) ORDER BY tipe ASC SEPARATOR ' - ') AS jam_masuk_pulang, 
-- TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 1), ' - ', -1))) AS jam_timediff, 
-- date(waktu) as tanggal
-- FROM `presensi` 
-- GROUP BY presensi.id_pegawai, tanggal;

-- DROP view view_rekap_presensi;
-- CREATE OR REPLACE VIEW view_rekap_presensi AS
-- SELECT presensi.id_pegawai, presensi.jam_masuk_pulang as jadwal_jam_masuk_pulang, 
-- TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1))) AS jadwal_timediff, 
-- GROUP_CONCAT(time(waktu) ORDER BY tipe ASC SEPARATOR ' - ') AS jam_masuk_pulang, 
-- TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 1), ' - ', -1))) AS jam_timediff, 
-- date(waktu) as tanggal
-- FROM `presensi` 
-- WHERE NOT EXISTS
-- (
--         SELECT tugas_dinas.tanggal
--         FROM tugas_dinas
--         WHERE tugas_dinas.tanggal = DATE(presensi.waktu)
-- )
-- GROUP BY presensi.id_pegawai, tanggal;

-- https://stackoverflow.com/questions/66353341/referencing-sub-query-in-multiple-conditions
-- DROP view IF EXISTS view_rekap_presensi;
-- CREATE OR REPLACE VIEW view_rekap_presensi AS
-- SELECT presensi.id_pegawai, presensi.jam_masuk_pulang as jadwal_jam_masuk_pulang, 
-- TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1))) AS jadwal_timediff, 
-- GROUP_CONCAT(time(waktu) ORDER BY tipe ASC SEPARATOR ' - ') AS jam_masuk_pulang, 
-- TIMEDIFF(time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 2), ' - ', -1)), time(SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(waktu ORDER BY tipe SEPARATOR ' - '), ' - ', 1), ' - ', -1))) AS jam_timediff, 
-- date(waktu) as tanggal
-- FROM `presensi` 
-- WHERE  (presensi.id_pegawai, DATE(presensi.waktu)) NOT IN
-- (
--     SELECT * FROM
--     (
--         SELECT tugas_dinas.id_pegawai, tugas_dinas.tanggal
--         FROM tugas_dinas
--         LEFT JOIN presensi ON presensi.id_pegawai = tugas_dinas.id_pegawai
--     ) AS subquery
-- )
-- GROUP BY presensi.id_pegawai, tanggal;


-- query durasi jam kerja dikurangi istirahat
-- note jam_timediff = '00:00:00.000000' berarti tidak absen pulang
-- kondisi jam absen pulang lebih dari jam istirahat
-- SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1) >= AddTime(SUBSTRING_INDEX(SUBSTRING_INDEX(jadwal_jam_istirahat, ' - ', 2), ' - ', -1)
SELECT SUM(TIME_TO_SEC(jadwal_timediff_istirahat)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = 1 AND jam_timediff != '00:00:00.000000' AND SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1) >= AddTime(SUBSTRING_INDEX(SUBSTRING_INDEX(jadwal_jam_istirahat, ' - ', 2), ' - ', -1), '00:01:00') AND tanggal BETWEEN '2022-06-21' AND '2022-06-30'


-- CREATE OR REPLACE VIEW view_list_unit_relations AS
-- SELECT unit_relations.id, uparent.nama_unit AS parent, uchild.nama_unit AS child
-- FROM `unit_relations` 
-- LEFT JOIN unit uparent on uparent.id = unit_relations.parent
-- LEFT JOIN unit uchild on uchild.id = unit_relations.child
-- WHERE depth < 2 
-- ORDER BY parent


-- CREATE OR REPLACE VIEW view_setting_penilaian_unit AS
-- SELECT setting_penilaian_unit.id, setting_penilaian_unit.id_jabatan_u_penilai, jabatan_u_penilai.nama_unit as nama_jabatan_u_penilai, setting_penilaian_unit.id_jabatan_u_dinilai, jabatan_u_dinilai.nama_unit as nama_jabatan_u_dinilai
-- FROM `setting_penilaian_unit` 
-- LEFT JOIN unit jabatan_u_penilai on jabatan_u_penilai.id = setting_penilaian_unit.id_jabatan_u_penilai
-- LEFT JOIN unit jabatan_u_dinilai on jabatan_u_dinilai.id = setting_penilaian_unit.id_jabatan_u_dinilai
-- ORDER BY `nama_jabatan_u_penilai` ASC;


-- CREATE OR REPLACE VIEW penilaian_setting_view AS
-- SELECT penilaian_setting.id, penilaian_setting.id_periode, ci4_persuratan2.periode.is_aktif, penilaian_setting.tanggal_mulai, penilaian_setting.tanggal_selesai
-- FROM penilaian_setting
-- LEFT JOIN ci4_persuratan2.periode ON ci4_persuratan2.periode.id = penilaian_setting.id_periode;


-- SELECT 
-- MIN(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1)), 
-- MAX(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1)), 
-- SUM(TIME_TO_SEC(TIMEDIFF(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1), SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1)))/3600) AS total_hours 
-- FROM view_rekap_presensi

-- SELECT
--   ROUND(SUM(TIMESTAMPDIFF(MINUTE, SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1), SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1)) / 60.0), 1) AS "Worked Hours" 
-- FROM `view_rekap_presensi`
-- where id_pegawai = 1

-- SELECT       
-- SEC_TO_TIME(SUM(TIME_TO_SEC(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1)) - TIME_TO_SEC(SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1)))) / 3600 AS timediff
-- FROM `view_rekap_presensi`
-- where id_pegawai < 2

-- -- https://stackoverflow.com/questions/19051544/sum-of-date-time-difference-in-a-mysql-query
-- its in decimal hours
-- https://www.ontheclock.com/convert-hours-minutes-to-decimal-hours.aspx
-- https://www.calculatorsoup.com/calculators/time/hours.php
-- SELECT       
-- SUM(TIME_TO_SEC(jam_timediff)) as total_seconds,
-- SUM(TIME_TO_SEC(jam_timediff)/3600) as total_hours
-- FROM `view_rekap_presensi`
-- where id_pegawai < 2

-- SELECT UNIX_TIMESTAMP('2010-08-20 12:01:00') - 
--        UNIX_TIMESTAMP('2010-08-20 12:00:00') diff FROM `view_rekap_presensi`
-- where id_pegawai < 3
-- GROUP BY presensi.id_pegawai, tanggal CAST(waktu AS DATE);




-- https://stackoverflow.com/questions/13295374/numeric-representation-of-the-day-of-the-week-mysql-vs-php
-- SELECT COUNT(*) as cuti FROM absensi WHERE id_pegawai = 63 AND jenis_absensi = 'Cuti' AND tanggal BETWEEN '2022-03-02' AND '2022-03-18' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) AND tanggal NOT IN ('2022-03-08')



-- SELECT pegawai.*, GROUP_CONCAT( concat( pegawai_jabatan_u_view.nama_jabatan," ",pegawai_jabatan_struktural_u_view.nama_jabatan_struktural," ",pegawai_jabatan_fungsional_view.nama_jabatan_fungsional,"" )  SEPARATOR ', ') AS jabatan
-- FROM pegawai
--  JOIN pegawai_jabatan_u_view ON pegawai_jabatan_u_view.id_pegawai = pegawai.id
--  JOIN pegawai_jabatan_struktural_u_view ON pegawai_jabatan_struktural_u_view.id_pegawai = pegawai.id
--  JOIN pegawai_jabatan_fungsional_view ON pegawai_jabatan_fungsional_view.id_pegawai = pegawai.id




-- DROP TABLE IF EXISTS presensi_acara_pegawai_view;
-- CREATE OR REPLACE VIEW presensi_acara_pegawai_view AS 
-- SELECT presensi_acara.*, pegawai.nama, acara.nama_acara, acara.barcode, acara.tanggal
-- FROM presensi_acara
-- LEFT JOIN pegawai ON pegawai.id = presensi_acara.id_pegawai
-- LEFT JOIN acara ON acara.id = presensi_acara.id_acara;

-- DROP TABLE IF EXISTS presensi_acara_peserta_view;
-- CREATE OR REPLACE VIEW presensi_acara_peserta_view AS 
-- SELECT presensi_acara.*, peserta_acara.nama, acara.nama_acara, acara.barcode, acara.tanggal
-- FROM presensi_acara
-- LEFT JOIN peserta_acara ON peserta_acara.id = presensi_acara.id_peserta
-- LEFT JOIN acara ON acara.id = presensi_acara.id_acara;

-- DROP TABLE IF EXISTS presensi_acara_view;
-- CREATE OR REPLACE VIEW presensi_acara_view AS 
-- SELECT presensi_acara_pegawai_view.id, presensi_acara_pegawai_view.waktu, presensi_acara_pegawai_view.nama_acara, presensi_acara_pegawai_view.barcode, presensi_acara_pegawai_view.tanggal, presensi_acara_pegawai_view.id_pegawai, null AS id_peserta, presensi_acara_pegawai_view.nama
-- FROM presensi_acara_pegawai_view
-- UNION
-- SELECT presensi_acara_peserta_view.id, presensi_acara_peserta_view.waktu, presensi_acara_peserta_view.nama_acara, presensi_acara_peserta_view.barcode, presensi_acara_peserta_view.tanggal, null AS id_pegawai, presensi_acara_peserta_view.id_peserta, presensi_acara_peserta_view.nama
-- FROM presensi_acara_peserta_view;










-- query terlmbat
SELECT COUNT(*) as terlambat
FROM view_rekap_presensi 
WHERE SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1) > ADDTIME(SUBSTRING_INDEX(SUBSTRING_INDEX(jadwal_jam_masuk_pulang, ' - ', 1), ' - ', -1), '00:15:00')

-- query izin & sakit
-- https://stackoverflow.com/questions/55156767/check-date-between-two-date-columns-from-two-dates-mysql
-- tanggal selesai kurang dari filter tanggal
SELECT sum(datediff(tanggal_selesai + INTERVAL 1 DAY, tanggal_mulai)) as sakit 
FROM absensi 
WHERE jenis_absensi = 'Sakit' AND tanggal_mulai >= '2022-02-01' AND tanggal_selesai <= '2022-02-28'

-- tanggal selesai lebih dari filter tanggal
SELECT sum(datediff('2022-02-28', tanggal_mulai)) as sakit 
FROM absensi 
WHERE jenis_absensi = 'Sakit' AND tanggal_mulai >= '2022-02-01' AND tanggal_selesai > '2022-02-28'

-- update query ganti jadwal
UPDATE `presensi` SET `jam_masuk_pulang`='08:00:00 - 15:00:00',`jam_istirahat`='12:00:00 - 13:00:00'
WHERE id_pegawai = 45 AND DATE(waktu) >= '2022-07-11'


-- INSERT INTO `jabatan_struktural_u`(`id_unit`,`id_jabatan_struktural`) 
-- VALUES 
-- (54,3),
-- (55,3),
-- (56,3),
-- (57,3),
-- (58,3),
-- (59,3),
-- (60,3),
-- (61,3),
-- (62,3),
-- (63,3),
-- (64,3),
-- (65,3),
-- (66,3),
-- (67,3),
-- (68,3),
-- (69,3),
-- (70,3),
-- (71,3),
-- (72,3)

SELECT * FROM jadwal_kerja_auto_detail_view 
WHERE id_jadwal_kerja_auto = 1 AND '07:00:00' BETWEEN jam_masuk AND jam_pulang
OR id_jadwal_kerja_auto = 1 AND '14:00:00' BETWEEN jam_masuk AND jam_pulang
OR id_jadwal_kerja_auto = 1 AND '07:00:00' BETWEEN jam_masuk AND jam_pulang AND '14:00:00' > jam_pulang
OR id_jadwal_kerja_auto = 1 AND '07:00:00' < jam_masuk AND '14:00:00' > jam_pulang


-- 
-- INSERT INTO pertanyaan_value (id_pertanyaan, nilai, value)
-- VALUES (12, 1, 4),(12, 2, 3),(12, 3, 2),(12, 4, 1);
-- INSERT INTO pertanyaan_value (id_pertanyaan, nilai, value)
-- VALUES (13, 1, 4),(13, 2, 3),(13, 3, 2),(13, 4, 1);
-- INSERT INTO pertanyaan_value (id_pertanyaan, nilai, value)
-- VALUES (14, 1, 4),(14, 2, 3),(14, 3, 2),(14, 4, 1);
-- INSERT INTO pertanyaan_value (id_pertanyaan, nilai, value)
-- VALUES (15, 1, 4),(15, 2, 3),(15, 3, 2),(15, 4, 1);