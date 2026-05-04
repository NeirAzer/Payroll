<div class="fi-resource-view-page bg-black">
    <div class="max-w-2xl mx-auto space-y-6">
        
        {{-- Card Informasi Pegawai --}}
        <section class="fi-section rounded-b-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex flex-col gap-y-1 border-b border-gray-100 p-6 dark:border-white/5">
                <h2 class="text-2xl font-semibold leading-6 text-primary-600">
                    Informasi Pegawai
                </h2>
            </div>

            <div class="fi-section-content p-6">
                <dl class="grid grid-cols-1 gap-y-4">
                    <div class="flex flex-col gap-y-1">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Pegawai</dt>
                        <dd class="text-sm text-gray-950 dark:text-white font-semibold">{{ $schedule->user->name }}</dd>
                    </div>
                    <div class="flex flex-col gap-y-1 border-t border-gray-50 pt-4 dark:border-white/5">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kantor</dt>
                        <dd class="text-sm text-gray-950 dark:text-white">{{ $schedule->office->name }}</dd>
                    </div>
                    <div class="flex flex-col gap-y-1 border-t border-gray-50 pt-4 dark:border-white/5">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        @if ($schedule->is_wfa)

                            <dd class="text-sm font-extrabold text-green-600">WFA</dd>
                            
                        @else
                        
                            <dd class="text-sm font-extrabold text-red-600">WFO</dd>

                        @endif
                    </div>
                    <div class="flex flex-col gap-y-1 border-t border-gray-50 pt-4 dark:border-white/5">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Shift</dt>
                        <dd class="text-sm text-gray-950 dark:text-white">
                            {{ $schedule->shift->name }} 
                            <span class="ml-2 text-xs text-gray-500">({{ $schedule->shift->start_time }} - {{ $schedule->shift->end_time }})</span>
                        </dd>
                    </div>
                </dl>

                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5 ring-1 ring-inset ring-gray-950/5 dark:ring-white/10">
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</div>
                        <div class="text-lg font-bold text-primary-600">{{ $attendance->start_time ?? '-'}}</div>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5 ring-1 ring-inset ring-gray-950/5 dark:ring-white/10">
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Keluar</div>
                        <div class="text-lg font-bold text-primary-600">{{ $attendance->end_time ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Card Presensi --}}
        <section class="fi-section rounded-t-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex flex-col gap-y-1 border-b border-gray-100 p-6 dark:border-white/5">
                <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    Lokasi Presensi
                </h2>
            </div>

            <div class="fi-section-content p-6">
                <div id="map" class="mb-6 rounded-lg border border-gray-200 dark:border-white/10 shadow-inner" style="height: 300px; z-index: 0;" wire:ignore></div>
                
                <form method="post" wire:submit="store" class="flex items-center justify-end gap-x-3">
                    <button 
                        type="button" 
                        onclick="tagLocation()"
                        class="fi-btn relative cursor-pointer grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-btn-color-gray fi-size-md fi-btn-with-label bg-white text-gray-950 ring-1 ring-gray-950/10 hover:bg-gray-50 px-4 py-2 text-sm shadow-sm"
                    >
                        Tag Location
                    </button>

                    @if ($insideRadius)
                        <button 
                            type="submit"
                            class="fi-btn relative cursor-pointer grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-btn-color-primary fi-size-md fi-btn-with-label bg-primary-600 text-white hover:bg-primary-500 px-4 py-2 text-sm shadow-sm"
                        >
                            Submit Presensi
                        </button>
                    @endif
                </form>
            </div>
        </section>
    </div>
</div>

<script>

    let marker;
    let office = [{{ $schedule->office->latitude }}, {{ $schedule->office->longitude }}];
    let radius = {{ $schedule->office->radius }};
    let map;
    let lat;
    let lng;
    let isWFA = @json($schedule->is_WFA);

    document.addEventListener('livewire:initialized', function () {
        component = @this;

        map = L.map('map').setView(office, 17);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);
    
    
        var circle = L.circle(office, {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: radius
        }).addTo(map);
    })


    function tagLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                lat = position.coords.latitude;
                lng = position.coords.longitude;
                
                if (marker) {
                    map.removeLayer(marker);
                }

                marker = L.marker([lat, lng]).addTo(map);
                marker.bindPopup('<b>Hello world!</b><br>I am a {{ $schedule->user->name }}.').openPopup();
                map.setView([lat, lng], 18);

                if (isWithinRadius(lat, lng, office, radius)) {
                    component.set('insideRadius', true);
                    component.set('latitute', lat);
                    component.set('longitude', lng);
                } else {

                    if (isWFA) {
                        component.set('insideRadius', true);
                        component.set('latitute', lat);
                        component.set('longitude', lng);
                    } else {
                        
                        alert('Presensi di luar jangkauan kantor!');

                    }
                }

            });
        } else {
            alert('Geolocation cannot tag the Location!');
        }
    }

    function isWithinRadius(lat, lng, center, radius) {
        let distance = map.distance([lat, lng], center );
        return distance <= radius;
    }
</script>

<style>
    /* Tambahan agar primary color sesuai tema Filament jika belum terdefinisi */
    :root {
        --primary-600: #eab308; /* Ganti dengan warna brand Anda (misal: kuning Filament) */
    }
    .bg-primary-600 { background-color: var(--primary-600); }
    .text-primary-600 { color: var(--primary-600); }
    .hover\:bg-primary-500:hover { background-color: #facc15; }
</style>