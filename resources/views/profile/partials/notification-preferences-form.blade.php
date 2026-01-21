<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Notifications
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Gerer les notifications email recues pour vos tickets.
        </p>
    </header>

    <form method="post" action="{{ route('profile.notifications.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <input type="hidden" name="notify_new_comment" value="0">
            <div class="flex items-start gap-3">
                <input
                    id="notify_new_comment"
                    name="notify_new_comment"
                    type="checkbox"
                    value="1"
                    class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    @checked(old('notify_new_comment', $user->notify_new_comment))
                >
                <div>
                    <label for="notify_new_comment" class="text-sm font-medium text-gray-900">
                        Nouveaux commentaires
                    </label>
                    <p class="text-sm text-gray-600">
                        Recevoir un email quand un nouveau commentaire est ajoute a un ticket.
                    </p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('notify_new_comment')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Enregistrer</x-primary-button>
        </div>
    </form>
</section>
