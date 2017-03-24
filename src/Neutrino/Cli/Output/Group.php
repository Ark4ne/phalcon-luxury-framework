<?php

namespace Neutrino\Cli\Output;

class Group
{
    protected $output;

    protected $datas;

    protected $groups = [];

    /**
     * Group constructor.
     *
     * @param ConsoleOutput $output
     * @param array         $datas
     */
    public function __construct(ConsoleOutput $output, array $datas = [])
    {
        $this->output = $output;
        $this->datas = $datas;
    }

    protected function generateGroupData()
    {
        foreach ($this->datas as $key => $data) {
            $washKey = Helper::removeDecoration($key);
            if (str_contains($washKey, ':')) {
                $keys = explode(':', $washKey);

                $group = $keys[0];

                $this->groups[$group][$key] = $data;
            } else {
                $this->groups['default'][$key] = $data;
            }
        }
    }

    public function display()
    {
        $this->generateGroupData();

        $tableOutput = new Table($this->output, [], [], Table::NO_STYLE);
        $table = [];
        foreach ($this->groups['default'] as $key => $value) {
            $table[] = [$key, $value];
        }
        $tableOutput->setDatas($table)->generateColumns();

        foreach ($this->groups as $group => $datas) {
            if ($group == 'default') {
                continue;
            }
            $table = [];
            foreach ($datas as $key => $value) {
                $table[] = [$key, $value];
            }
            $tableOutput->setDatas($table)->generateColumns();
        }

        foreach ($this->groups as $group => $datas) {
            if ($group != 'default') {
                $this->output->notice($group);
            }
            $table = [];
            foreach ($datas as $key => $value) {
                $table[] = [$key, $value];
            }
            $tableOutput->setDatas($table)->display(false);
        }
    }
}
