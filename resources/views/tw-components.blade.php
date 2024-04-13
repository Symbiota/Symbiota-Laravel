<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite(['resources/js/app.js','resources/css/app.css'])
    @stack('css-styles')
    @stack('js-libs')
</head>

<body class="min-h-screen flex flex-col w-[90%] max-w-screen-lg gap-y-4 mx-auto">
        <h1 class="text-3xl my-3 font-bold font-sans text-primary">
            Tailwind Components
        </h1>
        <x-swatch/>
        <x-button>Button</x-button>
        <button class="btn bg-transparent text-base-content border-secondary border w-fit" type="">Button</button>
        <x-button class="bg-secondary">Button</x-button>
        <x-input required :id="'input'" :label="'Input'" />
        <x-checkbox :id="'checkbox'" :label="'checkbox'" />
        <div class="bg-base-200">
        <x-brand/>
        </div>
</body>

</html>
