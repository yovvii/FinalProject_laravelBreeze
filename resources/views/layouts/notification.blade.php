{{-- Notification --}}
@if (session('success') || session('error'))
<div x-data="{ 
        show: true, 
        message: '{{ session('success') ?? session('error') }}',
        type: '{{ session('success') ? 'success' : 'error' }}' 
    }" 
    x-show="show" 
    x-init="setTimeout(() => show = false, 5000)" {{-- Notifikasi akan hilang setelah 5 detik --}}
    class="fixed top-0 left-[70%] right-0 z-9999 flex justify-center p-4 lg:transform-none lg:w-[30%] lg:translate-x-0 transform -translate-x-1/2 w-[80%]">

    <div :class="{ 
            'text-gray-700': type === 'success', 
            'text-gray-700': type === 'error' 
        }" 
        class="border rounded-lg shadow-md max-w-200% p-4 flex items-center justify-between bg-white transition-all duration-300">
        {{-- class="border rounded-lg shadow-md w-200% p-4 flex items-center justify-between bg-white transition-all duration-300"> --}}
        
        <div class="flex items-start">
            <div>
                <div class="flex items-center">
                    <template x-if="type === 'success'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="green" class="size-4">
                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm3.844-8.791a.75.75 0 0 0-1.188-.918l-3.7 4.79-1.649-1.833a.75.75 0 1 0-1.114 1.004l2.25 2.5a.75.75 0 0 0 1.15-.043l4.25-5.5Z" clip-rule="evenodd" />
                        </svg>
                    </template>
                    <template x-if="type === 'error'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="red" class="size-4">
                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm2.78-4.22a.75.75 0 0 1-1.06 0L8 9.06l-1.72 1.72a.75.75 0 1 1-1.06-1.06L6.94 8 5.22 6.28a.75.75 0 0 1 1.06-1.06L8 6.94l1.72-1.72a.75.75 0 1 1 1.06 1.06L9.06 8l1.72 1.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                        </svg>
                    </template>
                    <p class="font-black ms-1" x-text="type === 'success' ? 'Horeee!' : 'Sayang Sekali!'"></p>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600 ml-auto">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="text-[12px] md:text-sm" x-text="message"></p>
            </div>
            
            
        </div>
    </div>
</div>
@endif