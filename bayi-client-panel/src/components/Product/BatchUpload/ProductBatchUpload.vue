<script setup lang="ts">
import { useApi } from '/@src/composables/useApi';
import { useSwal } from '/@src/composables/useSwal';
import API_URIS from '/@src/utils/api/api_uris';
import { catchFieldError } from '/@src/utils/api/catchFormErrors';
const api = useApi();
const swal = useSwal();
const { ROOT, UTILS } = API_URIS;
const reqUri = ROOT + UTILS + '/product_batch_sample';
const templateUris = {
    json: reqUri + '/json',
    csv: reqUri + '/csv',
}

/* toplu ürün yükleme işlemleri */

const onFileChange = async (e: any) => {
    if (!e.target.files.length) return swal.fire('Hata', 'Dosya seçilmedi', 'error');
    const progressBar = document.querySelector('.progress-bar') as HTMLElement;
    progressBar.style.width = '0%';
    const file = e.target.files[0];
    const formData = new FormData();
    formData.append('file', file);
    try {
        progressBar.classList.remove('bg-danger');
        await api.postForm(API_URIS.PRODUCT + '/batch_upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            onUploadProgress: (progressEvent: any) => {
                const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                progressBar.style.width = percentCompleted + '%';
            }
        })
        swal.fire('Başarılı', 'Ürünler başarıyla yüklendi', 'success').then(() => {
            window.location.reload();
        })
    } catch (error) {
        progressBar.classList.add('bg-danger');
        const errs: string[] = catchFieldError(error);
        swal.fire({
            title: 'Hata',
            html: errs.join('<br>'),
            icon: 'error'
        });
    }
}


</script>

<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Toplu Ürün Yükle</h5>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label>Ürünlerin Yükleneceği Dosya</label>
                        <input type="file" class="form-control" ref="uploadbtn" @change="onFileChange" accept=".csv, .json">
                        <!-- loading bar -->
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                style="width: 0%"></div>
                        </div>
                        <!-- dosya örneği -->
                        <a :href="templateUris.csv" class="btn btn-outline-primary mt-2 mr-1" target="_blank">
                            <i class="icon-download4 mr-2"></i>
                            Örnek Dosyayı İndir(CSV)
                        </a>
                        <a :href="templateUris.json" class="btn btn-outline-primary mt-2" target="_blank">
                            <i class="icon-download4 mr-2"></i>
                            Örnek Dosyayı İndir(JSON)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>