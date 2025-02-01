<?php
/**
 * Buy Credits Modal Template
 * 
 * @package BrightsideAI
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- Buy Credits Modal -->
<div 
    id="buy-credits-modal" 
    x-data="{ selectedPackage: '100' }"
    tabindex="-1" 
    aria-hidden="true" 
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full"
>
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">
                        Purchase Credits
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Select the amount of credits you would like to purchase.
                    </p>
                </div>
                <button 
                    type="button" 
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center" 
                    data-modal-target="buy-credits-modal"
                    data-modal-hide="buy-credits-modal"
                >
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            
            <!-- Modal body -->
            <div class="p-4 md:p-5">
                <div class="grid grid-cols-3 gap-4">
                    <!-- 100 Credits Option -->
                    <div 
                        @click="selectedPackage = '100'"
                        class="border rounded-lg p-4 text-center cursor-pointer hover:bg-gray-50"
                        :class="selectedPackage === '100' ? 'border-black' : 'border-gray-200'"
                    >
                        <div class="text-2xl font-bold">100</div>
                        <div class="text-sm">Credits</div>
                        <div class="mt-1 font-medium">$10</div>
                    </div>

                    <!-- 500 Credits Option -->
                    <div 
                        @click="selectedPackage = '500'"
                        class="border rounded-lg p-4 text-center cursor-pointer hover:bg-gray-50"
                        :class="selectedPackage === '500' ? 'border-black' : 'border-gray-200'"
                    >
                        <div class="text-2xl font-bold">500</div>
                        <div class="text-sm">Credits</div>
                        <div class="mt-1 font-medium">$45</div>
                    </div>

                    <!-- 1000 Credits Option -->
                    <div 
                        @click="selectedPackage = '1000'"
                        class="border rounded-lg p-4 text-center cursor-pointer hover:bg-gray-50"
                        :class="selectedPackage === '1000' ? 'border-black' : 'border-gray-200'"
                    >
                        <div class="text-2xl font-bold">1000</div>
                        <div class="text-sm">Credits</div>
                        <div class="mt-1 font-medium">$80</div>
                    </div>
                </div>

                <!-- Purchase button -->
                <div class="mt-6 flex justify-end">
                    <button 
                        type="button" 
                        @click="purchaseCredits(selectedPackage)"
                        class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"
                    >
                        Purchase Credits
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>