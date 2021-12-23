<x-jet-form-section submit="addMeal">
    <x-slot name="title">
        {{ __('Create a new Recipe') }}
    </x-slot>

    <x-slot name="description">
        {{ __('You can add a new recipe for others to see, make sure it is delicious.') }}
    </x-slot>

    <x-slot name="form">

        {{-- name --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="name" value="{{ __('Recipe Name') }}" />
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
                      x-bind:style="'background-image: url(\'' + photoPreview + '\');'"
                      >
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

        {{-- time --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="time" value="{{ __('Time until cooked') }}" />
            <x-jet-input id="time" type="time" step="1" class="mt-1 block w-full" wire:model.lazy="time" autocomplete="time" />
            <x-jet-input-error for="time" class="mt-2" />
        </div>


        {{-- ingredients --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="ingredients" value="{{ __('Ingredients') }}" />

            @foreach ($beautyfilledingredients as $selecteding)
                <div class="float mt-1">
                    <x-jet-input disabled type="text" step="1" style="max-width:33%" value="{{$selecteding['name']}}"/>
                    <x-jet-input disabled type="text" step="1" style="max-width:33%" value="{{$selecteding['quantity']}}"/>
                    <x-jet-danger-button type="button" wire:click="removeIng({{$selecteding['id']}})">Remove</x-jet-danger-button>
                </div>
            @endforeach

            <div class="float mt-1">
                <select wire:model.lazy="newingid" id="ingredients" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value="null" selected></option>
                    @foreach ($ingredients as $item)
                        <option  value="{{$item['_id']}}">{{$item['name']}}</option>
                    @endforeach
                </select>
    
                <x-jet-input type="text" step="1" class="w-30" wire:model.lazy="newingqt" />

                <x-jet-button type="button" wire:click="addIng">Add</x-jet-button>
            </div>
            <x-jet-input-error for="newingid" class="mt-2" />
            <x-jet-input-error for="newingqt" class="mt-2" />
            <x-jet-input-error for="filledingredients" class="mt-2" />
        </div>
        

        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="steps" value="{{ __('Steps') }}" />
            <textarea id="steps" type="text" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full h-30" wire:model.lazy="steps" autocomplete="steps"></textarea>
            <x-jet-input-error for="steps" class="mt-2" />
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