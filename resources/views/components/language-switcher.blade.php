@php
    $languages = config('localization.languages', []);
    $currentLocale = app()->getLocale();
    $currentLang = collect($languages)->firstWhere('code', $currentLocale);
@endphp

<div class="dropdown dropdown-end">
    <label tabindex="0" class="btn btn-ghost btn-sm gap-1 h-8 min-h-8 px-2 cursor-pointer">
        @if ($currentLang)
            <span>{{ $currentLang['flag'] }}</span>
            <span class="hidden sm:inline text-xs uppercase">{{ $currentLocale }}</span>
        @else
            <span class="text-xs uppercase">{{ $currentLocale }}</span>
        @endif
    </label>
    <div tabindex="0" class="dropdown-content bg-base-100 z-[1] w-40 shadow-md border border-base-200 rounded-md p-1">
        @foreach ($languages as $lang)
            <form action="{{ route('locale.switch') }}" method="POST" class="w-full">
                @csrf
                <input type="hidden" name="locale" value="{{ $lang['code'] }}">
                <input type="hidden" name="redirect" value="{{ url()->current() }}">
                <button type="submit" class="flex w-full items-center gap-2 px-3 py-1.5 text-sm rounded-md cursor-pointer {{ $lang['code'] === $currentLocale ? 'font-semibold text-primary bg-primary/10' : 'hover:bg-base-200' }}">
                    <span>{{ $lang['flag'] }}</span>
                    <span>{{ $lang['name'] }}</span>
                </button>
            </form>
        @endforeach
    </div>
</div>
