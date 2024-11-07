<div class="flex flex-1 min-h-screen">
    <!-- Sidebar -->
    <aside class="flex flex-col w-64 bg-Main8 text-white px-10 py-12 relative">
        <!-- Admin -->
        <div class="flex justify-center items-center">
            <div class="w-32 h-32 bg-White rounded-full overflow-hidden shadow-Button">
                <img src="<?= base_url(); ?>asset/img/gojo.png" alt="" class="w-full h-full object-cover">
            </div>
        </div>
        <div class="flex justify-center items-center mt-5 text-black">

            <h3>Admin 1</h3>
            
        </div>
        <!-- Button -->
        <div class="flex w-full mt-8"> 
            <div class="flex w-full h-12 bg-Bg3 px-4 py-2 rounded-lg items-center shadow-Button hover:bg-Main9">
                <a href="<?= base_url(); ?>Rekam_medis/main">
                    <i class="fa-solid fa-file-medical text-black w-6"></i>
                    <label class="p-regular text-black">Data Pasien</label>
                </a>
            </div>
            </a>
        </div>
        <div class="flex w-full mt-5">
            <div class="flex w-full h-12 bg-Bg3 px-4 py-2 rounded-lg items-center shadow-Button hover:bg-Main9">
                <a href="<?= base_url(); ?>Rekam_medis/mainDokter">
                    <i class="fa-solid fa-file-medical text-black w-6"></i>
                    <label class="p-regular text-black">Data Dokter</label>
                </a>
            </div>
        </div>
        <div class="flex absolute bottom-12 left-12">
            <div class="flex w-10 h-10 bg-Button1-40 rounded-lg justify-center items-center shadow-Button hover:bg-Button1-default">
                <a href="<?= base_url(); ?>Rekam_medis">
                    <img src="<?= base_url(); ?>asset/img/logout-04.svg" alt="Logout" class="w-6 object-contain">
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 px-12 py-8 bg-Bg3">
        <div class="content w-full h-full mx-auto">
            <div class="flex w-full h-auto items-center">
                <img src="<?= base_url(); ?>asset/img/Shield.png" alt="" class="w-16">
                <h2>PENS HOSPITAL</h2>
            </div>

            <div class="w-full rounded-3xl p-8 mt-8 bg-Bg4-30 shadow-Card">
                <div class="header mb-5">
                    <h3 class="text-center">REKAM MEDIS PASIEN</h3>
                </div>

                <script>
                    // Cek apakah ada flashdata sukses
                    <?php if ($this->session->flashdata('success')): ?>
                        Swal.fire({
                            title: 'Berhasil!',
                            text: '<?= $this->session->flashdata('success'); ?>',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    <?php endif; ?>
                </script>


                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 px-4 py-2">
                    <div class="flex flex-col space-y-2">
                        <?php
                        $apiUrl = "https://rawat-jalan.pockethost.io/api/collections/pasien/records";
                        $data = json_decode(file_get_contents($apiUrl), true);

                        if (isset($data['items']) && is_array($data['items']) && !empty($data['items'])):
                            // Mengambil data pasien pertama
                            $pasien = $data['items'][0];
                            ?>
                            <div class="flex">
                                <span class="p-regular w-32">Nama</span>
                                <span>: <?= htmlspecialchars($pasien["nama_lengkap"]); ?></span>
                            </div>
                            <div class="flex">
                                <span class="p-regular w-32">Jenis Kelamin</span>
                                <span>: <?= htmlspecialchars($pasien["jenis_kelamin"]); ?></span>
                            </div>
                            <div class="flex">
                                <span class="p-regular w-32">Tanggal Lahir</span>
                                <span>: <?= htmlspecialchars($pasien["tanggal_lahir"]); ?></span>
                            </div>
                        </div>
                        <div class="flex flex-col space-y-2 mr-20">
                            <div class="flex">
                                <span class="p-regular w-32">Nama Ibu</span>
                                <span>: <?= htmlspecialchars($pasien["nama_ibu"]); ?></span>
                            </div>
                            <div class="flex">
                                <span class="p-regular w-32">Alamat</span>
                                <span>: <?= htmlspecialchars($pasien["alamat"]); ?></span>
                            </div>
                            <div class="flex">
                                <span class="p-regular w-32">No. Telepon</span>
                                <span>: <?= htmlspecialchars($pasien["no_telp"]); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="w-full rounded-3xl p-10 my-8 bg-Bg4-30 shadow-Card">
                <table class="table w-full">
                    <thead class="bg-Main8 text-white">
                        <tr>
                            <th class="p-light text-center">No</th>
                            <th class="p-light text-center">Tanggal Masuk</th>
                            <th class="p-light text-center">Tanggal Keluar</th>
                            <th class="p-light text-center">Diagnosa</th>
                            <th class="p-light text-center">Obat</th>
                            <th class="p-light text-center">Edit Rekam Medis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $apiUrl = "http://localhost:3000/api/rekam-medis/";
                        $data = json_decode(file_get_contents($apiUrl), true);

                        if (isset($data['payload']) && is_array($data['payload'])):
                            $i = 1; // Inisialisasi variabel counter
                            foreach ($data['payload'] as $item):
                                ?>
                                <tr>
                                    <th class="p-light text-center"><?= $i++; ?></th>
                                    <td class="p-light text-center"><?= htmlspecialchars($item["Tanggal_MRS"]); ?></td>
                                    <td class="p-light text-center"><?= htmlspecialchars($item["Tanggal_KRS"]); ?></td>
                                    <td class="p-light text-center"><?= htmlspecialchars($item["Diagnosa"]); ?></td>
                                    <td class="p-light text-center"><?= htmlspecialchars($item["Obat"]); ?></td>
                                    <td class="p-light text-center">
                                        <a href="<?= base_url(); ?>Rekam_medis/edit/<?= htmlspecialchars($item['NO_RekamMedis']); ?>"
                                            class="text-Main7 hover:text-Main9">
                                            <i class="fa-solid fa-pen-to-square fa-lg"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        else:
                            echo "<tr><td colspan='6' class='text-center'>Data rekam medis tidak tersedia.</td></tr>";
                        endif;
                        ?>
                    </tbody>
                </table>
                <br>
                <div class="flex justify-end">
                    <a href="<?= base_url('Rekam_medis/tambahRekamMedis/' . $pasien['id']); ?>">
                        <button class="p-regular btn bg-Main8 hover:bg-Main9 text-white px-3 py-1 shadow-Button">
                            <i class="fa-solid fa-circle-plus fa-lg"></i>
                            Tambah Rekam Medis
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>