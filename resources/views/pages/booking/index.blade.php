@extends('layouts.public')

@section('title', __('booking.pageTitle'))
@section('meta_description', __('booking.pageDescription'))

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    /* Swiper wheel custom styles */
    .time-swiper {
        width: 100%;
        height: 260px;
    }

    .time-swiper .swiper-wrapper {
        align-items: center;
    }

    .time-swiper .swiper-slide {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 44px !important;
        width: 80%;
        flex-shrink: 0;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0.25;
        transform: scale(0.85);
        user-select: none;
    }

    .time-swiper .swiper-slide-active {
        opacity: 1;
        transform: scale(1);
        z-index: 2;
        background: var(--p, hsl(var(--p)));
        color: var(--pc, hsl(var(--pc)));
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    }

    .time-swiper .swiper-slide-prev,
    .time-swiper .swiper-slide-next {
        opacity: 0.6;
        transform: scale(0.92);
    }

    /* Skeleton animation */
    .skeleton-pulse {
        animation: skeleton-pulse 1.5s ease-in-out infinite;
    }

    @keyframes skeleton-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-12 max-w-4xl" x-data="bookingWizard('{{ $timeSlotStyle }}')" x-init="init()" x-cloak>
    {{-- Header --}}
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold mb-2">{{ __('booking.pageTitle') }}</h1>
        <p class="text-muted-foreground">{{ __('booking.pageSubtitle') }}</p>
    </div>

    {{-- Step Indicators --}}
    <div class="flex items-center justify-center mb-10">
        <template x-for="(stepLabel, idx) in ['{{ __('booking.step1') }}', '{{ __('booking.step2') }}', '{{ __('booking.step3') }}']" :key="idx">
            <div class="flex items-center">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold"
                         :class="step > idx + 1 ? 'bg-primary text-primary-foreground' : (step === idx + 1 ? 'bg-primary/20 text-primary ring-2 ring-primary' : 'bg-muted text-muted-foreground')">
                        <span x-show="step > idx + 1">✓</span>
                        <span x-show="step <= idx + 1" x-text="idx + 1"></span>
                    </div>
                    <span class="text-xs mt-1 font-medium" :class="step === idx + 1 ? 'text-primary' : 'text-muted-foreground'" x-text="stepLabel"></span>
                </div>
                <div x-show="idx < 2" class="w-16 sm:w-24 h-0.5 mx-2" :class="step > idx + 1 ? 'bg-primary' : 'bg-muted'"></div>
            </div>
        </template>
    </div>

    {{-- Success Message --}}
    <div x-show="success" class="mb-6 alert alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span x-text="successMessage"></span>
    </div>

    {{-- Error Message --}}
    <div x-show="error" class="mb-6 alert alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m0 0l2 2m0 0l2-2m0 0l-2 2m0 0l-2-2m0 0l-2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span x-text="errorMessage"></span>
    </div>

    {{-- Step 1: Select Service --}}
    <div x-show="step === 1" class="space-y-4">
        <h2 class="text-xl font-semibold mb-4">{{ __('booking.selectService') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($services as $service)
            <div class="card border cursor-pointer hover:border-primary transition-colors"
                 :class="form.booking_service_id === {{ $service->id }} ? 'border-primary bg-primary/5' : ''"
                 @click="selectService({{ $service->id }}, '{{ addslashes($service->translated('name')) }}', {{ $service->duration_minutes }}, '{{ $service->price ?? '' }}')">
                <div class="card-body p-5">
                    <h3 class="card-title text-lg">{{ $service->translated('name') }}</h3>
                    @if($service->translated('description'))
                    <p class="text-sm text-muted-foreground">{{ $service->translated('description') }}</p>
                    @endif
                    <div class="flex items-center gap-4 mt-2 text-sm">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $service->duration_minutes }} {{ __('booking.minutes') }}
                        </span>
                        @if($service->price)
                        <span class="font-semibold">{{ $service->price }} {{ __('booking.currency') }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if($services->isEmpty())
        <div class="text-center py-8 text-muted-foreground">{{ __('booking.noServices') }}</div>
        @endif
        <div class="flex justify-end mt-6">
            <button class="btn btn-primary" :disabled="!form.booking_service_id" @click="goToStep(2)">
                {{ __('booking.next') }}
            </button>
        </div>
    </div>

    {{-- Step 2: Select Date & Time --}}
    <div x-show="step === 2" class="space-y-6">
        <h2 class="text-xl font-semibold">{{ __('booking.selectDateTime') }}</h2>

        {{-- Combined Calendar + Time Panel --}}
        <div class="flex flex-col md:flex-row gap-4 justify-center items-start max-w-3xl mx-auto"
             :class="isRtl ? 'md:flex-row-reverse' : ''">

            {{-- Calendar Card --}}
            <div class="rounded-2xl border bg-card p-5 max-w-sm w-full shrink-0 transition-all duration-500 ease-out">
                {{-- Month Navigation --}}
                <div class="flex items-center justify-between mb-4">
                    <button class="btn btn-ghost btn-sm" @click="prevMonth()">
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <h3 class="font-semibold text-lg" x-text="monthName + ' ' + currentYear"></h3>
                    <button class="btn btn-ghost btn-sm" @click="nextMonth()">
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>

                {{-- Day Names --}}
                <div class="grid grid-cols-7 gap-1 mb-2">
                    <template x-for="dayName in dayNames" :key="dayName">
                        <div class="text-center text-xs font-medium text-muted-foreground py-1" x-text="dayName"></div>
                    </template>
                </div>

                {{-- Calendar Grid --}}
                <div class="grid grid-cols-7 gap-1">
                    <template x-for="blank in firstDayOffset" :key="'blank-' + blank">
                        <div class="aspect-square"></div>
                    </template>

                    <template x-for="day in daysInMonth" :key="day">
                        <button
                            class="aspect-square rounded-lg text-sm font-medium relative transition-colors flex items-center justify-center"
                            :class="getDayClasses(day)"
                            :disabled="isDayDisabled(day)"
                            @click="selectDay(day)">
                            <span x-text="day"></span>
                            <span x-show="hasAvailableSlots(day)" class="absolute bottom-1 w-1 h-1 rounded-full bg-primary"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Time Panel Card (fixed height container, no layout shift) --}}
            <div class="rounded-2xl border bg-card overflow-hidden transition-all duration-500 ease-out"
                 :class="panelOpen
                     ? 'w-full md:w-72 min-h-[280px] max-h-[280px] p-5'
                     : 'w-full md:w-0 max-h-0 opacity-0 p-0 border-0 md:p-0 md:border-0 md:min-h-0'">

                {{-- Loading skeleton (fixed placeholders, no layout shift) --}}
                <div x-show="loadingSlots" class="flex flex-col items-center justify-center gap-2 py-4" style="height: 260px;">
                    <template x-for="i in 5" :key="i">
                        <div class="skeleton-pulse rounded-xl" style="height: 36px; width: 80%; background: hsl(var(--b3, var(--b3, #e5e7eb)));"></div>
                    </template>
                </div>

                {{-- Wheel Mode --}}
                <template x-if="timeSlotStyle === 'wheel' && !loadingSlots">
                    <div>
                        {{-- No slots --}}
                        <div x-show="slots.length === 0" class="flex flex-col items-center justify-center text-center py-8" style="height: 260px;">
                            <svg class="w-12 h-12 mx-auto text-muted-foreground/40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-sm text-muted-foreground">{{ __('booking.noSlots') }}</p>
                        </div>

                        {{-- Swiper --}}
                        <div x-show="slots.length > 0" class="time-swiper swiper" x-ref="timePanelSwiper">
                            <div class="swiper-wrapper">
                                <template x-for="(slot, index) in slots" :key="slot.start_time">
                                    <div class="swiper-slide" x-text="slot.start_time"></div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- List Mode --}}
                <template x-if="timeSlotStyle === 'list' && !loadingSlots">
                    <div class="h-full overflow-y-auto">
                        {{-- No slots --}}
                        <div x-show="slots.length === 0" class="flex flex-col items-center justify-center text-center py-8" style="height: 260px;">
                            <svg class="w-12 h-12 mx-auto text-muted-foreground/40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-sm text-muted-foreground">{{ __('booking.noSlots') }}</p>
                        </div>

                        {{-- Grouped list --}}
                        <div x-show="slots.length > 0" class="space-y-4">
                            <template x-for="group in groupedSlots" :key="group.label">
                                <div>
                                    <h4 class="text-xs font-semibold text-muted-foreground uppercase mb-2" x-text="group.label"></h4>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="slot in group.slots" :key="slot.start_time">
                                            <button class="px-3 py-1.5 rounded-full text-sm transition-colors"
                                                    :class="form.start_time === slot.start_time
                                                        ? 'bg-primary text-primary-foreground'
                                                        : 'bg-base-200 hover:bg-base-300'"
                                                    @click="selectTime(slot.start_time)"
                                                    x-text="slot.start_time">
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

        </div>

        <div class="flex justify-between mt-6">
            <button class="btn btn-ghost" @click="goToStep(1)">{{ __('booking.back') }}</button>
            <button class="btn btn-primary" :disabled="!form.date || !form.start_time" @click="goToStep(3)">
                {{ __('booking.next') }}
            </button>
        </div>
    </div>

    {{-- Step 3: Your Details --}}
    <div x-show="step === 3" class="space-y-6">
        <h2 class="text-xl font-semibold">{{ __('booking.yourDetails') }}</h2>

        {{-- Summary --}}
        <div class="card bg-base-200">
            <div class="card-body p-5 space-y-2">
                <h3 class="font-semibold">{{ __('booking.bookingSummary') }}</h3>
                <div class="text-sm space-y-1">
                    <p><span class="text-muted-foreground">{{ __('booking.service') }}:</span> <span x-text="selectedServiceName"></span></p>
                    <p><span class="text-muted-foreground">{{ __('booking.date') }}:</span> <span x-text="selectedDateLabel"></span></p>
                    <p><span class="text-muted-foreground">{{ __('booking.time') }}:</span> <span x-text="form.start_time"></span></p>
                    <p><span class="text-muted-foreground">{{ __('booking.duration') }}:</span> <span x-text="selectedDuration + ' ' + '{{ __('booking.minutes') }}'"></span></p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form @submit.prevent="submitBooking()" class="space-y-4">
            <div>
                <label class="label"><span class="label-text">{{ __('booking.fullName') }} *</span></label>
                <input type="text" x-model="form.customer_name" required class="input input-bordered w-full" placeholder="{{ __('booking.namePlaceholder') }}" />
            </div>
            <div>
                <label class="label"><span class="label-text">{{ __('booking.phone') }} *</span></label>
                <input type="tel" x-model="form.customer_phone" required class="input input-bordered w-full" placeholder="{{ __('booking.phonePlaceholder') }}" />
            </div>
            <div>
                <label class="label"><span class="label-text">{{ __('booking.notes') }}</span></label>
                <textarea x-model="form.notes" rows="3" class="textarea textarea-bordered w-full" placeholder="{{ __('booking.notesPlaceholder') }}"></textarea>
            </div>

            <div class="flex justify-between mt-6">
                <button type="button" class="btn btn-ghost" @click="goToStep(2)">{{ __('booking.back') }}</button>
                <button type="submit" class="btn btn-primary" :disabled="submitting">
                    <span x-show="!submitting">{{ __('booking.confirmBooking') }}</span>
                    <span x-show="submitting" class="loading loading-spinner loading-sm"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
    @vite('resources/js/booking.js')
@endpush
@endsection
