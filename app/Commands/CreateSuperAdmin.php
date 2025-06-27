<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateSuperAdmin extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'superadmin';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'admin:create-superadmin';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Create a superadmin user for the application';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'admin:create-superadmin [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--username' => 'Username for the superadmin',
        '--email'    => 'Email for the superadmin',
        '--password' => 'Password for the superadmin',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Creating Superadmin User', 'yellow');
        CLI::newLine();

        CLI::write("This command will create a superadmin user for the application.", 'green');
        CLI::newLine();

        // Get parameters from CLI options or use defaults
        $username = CLI::getOption('username') ?? 'admin';
        $email = CLI::getOption('email') ?? 'tecnologia@pmdonaines.pb.gov.br';
        $password = CLI::getOption('password') ?? 'admin';

        CLI::write("Using the following credentials:", 'cyan');
        CLI::write("Username: {$username}", 'white');
        CLI::write("Email: {$email}", 'white');
        CLI::write("Password: {$password}", 'white');
        CLI::newLine();

        try {
            $userProvider = auth()->getProvider();

            // Check if username already exists
            $existingUser = $userProvider->findByCredentials(['username' => $username]);
            if ($existingUser) {
                CLI::write("Username '{$username}' already exists!", 'yellow');
                
                // Check if user is already a superadmin
                if ($existingUser->inGroup('superadmin')) {
                    CLI::write('✓ User is already a superadmin!', 'green');
                } else {
                    // Add to superadmin group
                    $existingUser->addGroup('superadmin');
                    CLI::write('✓ Added existing user to superadmin group!', 'green');
                }
                
                CLI::newLine();
                CLI::write("Username: {$username}", 'cyan');
                CLI::write("Email: {$existingUser->email}", 'cyan');
                CLI::write("Group: superadmin", 'cyan');
                CLI::newLine();
                CLI::write('You can now access the admin panel at: /admin', 'yellow');
                return;
            }

            // Check if email already exists
            $existingEmailUser = $userProvider->findByCredentials(['email' => $email]);
            if ($existingEmailUser) {
                CLI::error("Email '{$email}' already exists!");
                return;
            }

            // Create user
            $userEntity = new \CodeIgniter\Shield\Entities\User([
                'username' => $username,
            ]);

            // Set email and password
            $userEntity->email = $email;
            $userEntity->password = $password;

            $userProvider->save($userEntity);

            // Reload the user to ensure we have the ID
            $userEntity = $userProvider->findByCredentials(['username' => $username]);
            
            if (!$userEntity) {
                CLI::error("Failed to retrieve created user!");
                return;
            }

            // Add to superadmin group
            $userEntity->addGroup('superadmin');

            CLI::write('✓ Superadmin user created successfully!', 'green');
            CLI::newLine();
            CLI::write("Username: {$username}", 'cyan');
            CLI::write("Email: {$email}", 'cyan');
            CLI::write("Group: superadmin", 'cyan');
            CLI::newLine();
            CLI::write('You can now access the admin panel at: /admin', 'yellow');

        } catch (\Exception $e) {
            CLI::error('Error creating superadmin user: ' . $e->getMessage());
        }
    }
}
