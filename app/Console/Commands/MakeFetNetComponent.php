<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeFetNetComponent extends Command
{
    protected $signature = 'make:fetnet {name}';
    protected $description = 'Generate Livewire Component, Blade View, and App/Guest Layout for FetNet';

    public function handle()
    {
        $name = $this->argument('name');
        $kebabName = Str::kebab(Str::snake(class_basename($name)));
        $classPath = app_path("Livewire/{$name}.php");
        $viewPath = resource_path("views/livewire/{$kebabName}.blade.php");

        // 1. Generate Livewire Class
        if (!File::exists($classPath)) {
            File::ensureDirectoryExists(dirname($classPath));
            File::put($classPath, <<<PHP
<?php

namespace App\Livewire;

use Livewire\Component;

class {$name} extends Component
{
    public function render()
    {
        return view('livewire.{$kebabName}');
    }
}
PHP);
            $this->info("✅ Livewire class created: {$classPath}");
        }

        // 2. Generate Blade View
        if (!File::exists($viewPath)) {
            File::ensureDirectoryExists(dirname($viewPath));
            File::put($viewPath, <<<BLADE
<div>
    <!-- {$name} Blade View -->
</div>
BLADE);
            $this->info("✅ Blade view created: {$viewPath}");
        }

        // 3. Layout Component Class
        $this->makeLayoutClass('AppLayout');
        $this->makeLayoutClass('GuestLayout');

        // 4. Layout Blade
        $this->makeLayoutView('app-layout');
        $this->makeLayoutView('guest-layout');

        $this->info("🎉 Done generating Livewire + Layout setup for: {$name}");
    }

    protected function makeLayoutClass($name)
    {
        $path = app_path("View/Components/{$name}.php");
        if (!File::exists($path)) {
            File::ensureDirectoryExists(app_path('View/Components'));
            File::put($path, <<<PHP
<?php

namespace App\View\Components;

use Illuminate\View\Component;

class {$name} extends Component
{
    public function render()
    {
        return view('components.{$this->kebab($name)}');
    }

    protected function kebab(\$string)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-\$0', \$string));
    }
}
PHP);
            $this->info("🧱 Layout component class created: {$path}");
        }
    }

    protected function makeLayoutView($fileName)
    {
        $path = resource_path("views/components/{$fileName}.blade.php");
        if (!File::exists($path)) {
            File::ensureDirectoryExists(resource_path('views/components'));
            File::put($path, <<<BLADE
<div class="min-h-screen bg-gray-100">
    <!-- {$fileName} layout slot -->
    {{ \$slot }}
</div>
BLADE);
            $this->info("🧾 Layout Blade created: {$path}");
        }
    }
}
