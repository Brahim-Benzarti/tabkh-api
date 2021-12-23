<x-jet-form-section submit="addIngredient">
    <x-slot name="title">
        {{ __('Add a new Ingredient') }}
    </x-slot>

    <x-slot name="description">
        {{ __('You can add a new ingredient and specify it\'s price as well ass nutricious information in order to calculate the approximate calories of each recipe.') }}
    </x-slot>

    <x-slot name="form">

        {{-- name --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="name" value="{{ __('Ingredient Name') }}" />
            <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.lazy="name" autocomplete="name" />
            <x-jet-input-error for="name" class="mt-2" />
        </div>


        {{-- picture --}}
        <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
            <input type="file" class="hidden"
                        wire:model="picture"
                        x-ref="picture"
                        x-on:change="
                                photoName = $refs.picture.files[0].name;
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    photoPreview = e.target.result;
                                };
                                reader.readAsDataURL($refs.picture.files[0]);
                        " />
            <x-jet-label for="picture" value="{{ __('Picture') }}" />
            <div class="mt-2" x-show="photoPreview">
                <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                      x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                </span>
            </div>

            <x-jet-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.picture.click()">
                {{ __('Select A Picture') }}
            </x-jet-secondary-button>

            @if ($this->picture)
                <x-jet-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                    {{ __('Remove Picture') }}
                </x-jet-secondary-button>
            @endif

            <x-jet-input-error for="picture" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="price" value="{{ __('Price (100 unit)') }}" />
            <x-jet-input id="price" type="text" class="mt-1 block w-full" wire:model.lazy="price" autocomplete="price" />
            <x-jet-input-error for="price" class="mt-2" />
        </div>


        {{-- Nutrition --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="fat" value="{{ __('Fat (in 100g)') }}" />
            <x-jet-input id="fat" type="text" class="mt-1 block w-full" wire:model.lazy="fat" autocomplete="fat" />
            <x-jet-input-error for="fat" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="protein" value="{{ __('Protein (in 100g)') }}" />
            <x-jet-input id="protein" type="text" class="mt-1 block w-full" wire:model.lazy="protein" autocomplete="protein" />
            <x-jet-input-error for="protein" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="carbohydrates" value="{{ __('Carbohydrates (in 100g)') }}" />
            <x-jet-input id="carbohydrates" type="text" class="mt-1 block w-full" wire:model.lazy="carbohydrates" autocomplete="carbohydrates" />
            <x-jet-input-error for="carbohydrates" class="mt-2" />
        </div>

        {{-- description --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="description" value="{{ __('Description') }}" />
            <textarea id="description" type="text" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full h-30" wire:model.lazy="description" autocomplete="description"></textarea>
            <x-jet-input-error for="description" class="mt-2" />
        </div>

        {{-- home made --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="hm">
            <x-jet-checkbox id="hm" wire:model.lazy="hm"/>{{ __('Home made') }}
            </x-jet-label>
        </div>

    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-action-message class="mr-3" on="error">
            {{ __('Something went wrong.') }}
        </x-jet-action-message>

        <x-jet-button>
            {{ __('Save') }}
        </x-jet-button>
    </x-slot>
</x-jet-form-section>