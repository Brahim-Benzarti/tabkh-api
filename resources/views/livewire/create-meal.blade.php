<x-jet-form-section submit="addMeal">
    <x-slot name="title">
        {{ __('Create a new Recipe') }}
    </x-slot>

    <x-slot name="description">
        {{ __('You can add a new recipe for others to see, make sure it is delicious.') }}
    </x-slot>

    <x-slot name="form">
        {{-- country --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="country" value="{{ __('Country') }}" />
            <select wire:model.lazy="country" style="max-width:33%" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                <option value="null" selected>New</option>
                @foreach ($countries as $key => $item)
                    <option  value="{{$key}}">{{$item}}</option>
                @endforeach
            </select>
            @if($country=="null")
            <x-jet-input type="text" step="1" style="max-width:43%" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" wire:model.lazy="newcountry" />
            <x-jet-input type="text" step="1" style="max-width:23%" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" wire:model.lazy="newcountrycode" />
            @endif
            <x-jet-input-error for="country" class="mt-2" />
        </div>


        {{-- name --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="name" value="{{ __('Recipe Name') }}" />
            <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.lazy="name" autocomplete="name" />
            <x-jet-input-error for="name" class="mt-2" />
        </div>


        {{-- local name --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="lname" value="{{ __('Recipe local name') }}" />
            <x-jet-input id="lname" type="text" class="mt-1 block w-full" wire:model.lazy="lname" autocomplete="lname" />
            <x-jet-input-error for="lname" class="mt-2" />
        </div>


        {{-- category --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="category" value="{{ __('Category') }}" />
            <select wire:model.lazy="category" style="max-width:50%" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                <option value="null" selected>New</option>
                @foreach ($categories as $item)
                    <option  value="{{$item}}">{{$item}}</option>
                @endforeach
            </select>
            @if($category=="null")
            <x-jet-input type="text" step="1" style="max-width:50%" id="category" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" wire:model.lazy="newcategory" autocomplete="category" />
            <x-jet-input-error for="category" class="mt-2" />
            @endif
        </div>

        
        {{-- picture --}}
        <!-- <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
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
        </div> -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="picture" value="{{ __('Cooking picture (minutes)') }}" />
            <x-jet-input id="picture" type="text" step="1" class="mt-1 block w-full" wire:model.lazy="picture" autocomplete="picture" />
            <x-jet-input-error for="picture" class="mt-2" />
        </div>

        {{-- time --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="time" value="{{ __('Cooking time (minutes)') }}" />
            <x-jet-input id="time" type="text" step="1" class="mt-1 block w-full" wire:model.lazy="time" autocomplete="time" />
            <x-jet-input-error for="time" class="mt-2" />
        </div>


        {{-- ingredients --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="ingredients" value="{{ __('Ingredients') }}" />

            @foreach ($beautyfilledingredients as $selecteding)
                <div class="float mt-1">
                    <x-jet-input disabled type="text" step="1" style="max-width:33%" value="{{$selecteding['name']}}"/>
                    <x-jet-input disabled type="text" step="1" style="max-width:18%" value="{{$selecteding['quantity']}}"/>
                    <x-jet-input disabled type="text" step="1" style="max-width:15%" value="{{$selecteding['unit']}}"/>
                    <x-jet-danger-button type="button" wire:click="removeIng({{$selecteding['id']}})">Remove</x-jet-danger-button>
                </div>
            @endforeach

            <div class="float mt-1">
                <select wire:model.lazy="newingid" wire:change="getUnits()" id="ingredients" style="height: 42px" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value="null" selected></option>
                    @foreach ($ingredients as $item)
                        <option  value="{{$item['_id']}}">{{$item['name']}}</option>
                    @endforeach
                </select>
    
                <x-jet-input type="text" step="1" class="w-30" wire:model.lazy="newingqt" />

                <select wire:model.lazy="newingunit"  style="height: 42px" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value="null" selected></option>
                    @foreach ($units as $item)
                        <option  value="{{$item}}">{{$item}}(s)</option>
                    @endforeach
                </select>

                <x-jet-button type="button" wire:click="addIng">Add</x-jet-button>
            </div>
            <x-jet-input-error for="newingid" class="mt-2" />
            <x-jet-input-error for="newingqt" class="mt-2" />
            <x-jet-input-error for="newingunit" class="mt-2" />
            <x-jet-input-error for="filledingredients" class="mt-2" />
        </div>
        


        {{-- Steps --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="steps" value="{{ __('Steps') }}" />
            <textarea id="steps" type="text" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full h-30" wire:model.lazy="steps" autocomplete="steps"></textarea>
            <x-jet-input-error for="steps" class="mt-2" />
        </div>


        {{-- lsteps --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="lsteps" value="{{ __('Local language steps') }}" />
            <textarea id="lsteps" type="text" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full h-30" wire:model.lazy="lsteps" autocomplete="lsteps"></textarea>
            <x-jet-input-error for="lsteps" class="mt-2" />
        </div>


        {{-- location --}}
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="" value="{{ __('Location') }}" />
            <x-jet-input type="text" step="1" style="max-width:43%" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" wire:model.lazy="latitude" />
            <x-jet-input type="text" step="1" style="max-width:23%" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" wire:model.lazy="longitude" />
            <x-jet-input-error for="" class="mt-2" />
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