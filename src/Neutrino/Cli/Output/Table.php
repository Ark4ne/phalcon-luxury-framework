<?php

namespace Neutrino\Cli\Output;

/**
 * Class Table
 *
 *  @package Neutrino\Cli\Output
 */
class Table
{
    const STYLE_DEFAULT = 'default';

    const NO_STYLE = 'no-style';

    protected $output;

    protected $datas;

    protected $columns = [];

    protected $style;

    /**
     * Table constructor.
     *
     * @param ConsoleOutput $output
     * @param array         $datas
     * @param array         $headers
     * @param string        $style
     */
    public function __construct(
        ConsoleOutput $output,
        array $datas = [],
        array $headers = [],
        $style = self::STYLE_DEFAULT
    ) {
        $this->output = $output;
        $this->datas  = $datas;
        $this->style  = $style;

        foreach ($headers as $header) {
            $this->columns[$header] = [];
        }
    }

    /**
     * @param array $datas
     *
     * @return $this
     */
    public function setDatas(array $datas)
    {
        $this->datas = $datas;

        return $this;
    }

    public function generateColumns()
    {
        foreach ($this->datas as $data) {
            foreach ($data as $column => $value) {
                if (!arr_has($this->columns, $column) || !arr_has($this->columns[$column], 'size')) {
                    $this->columns[$column] = [
                        'size' => max(Helper::strlenWithoutDecoration($column), Helper::strlenWithoutDecoration($value))
                    ];
                    continue;
                }

                $this->columns[$column]['size'] =
                    max($this->columns[$column]['size'], Helper::strlenWithoutDecoration($value));
            }
        }

        return $this;
    }

    protected function separator()
    {
        if ($this->style === self::NO_STYLE) {
            return;
        }
        $line = '+';
        foreach ($this->columns as $column => $opts) {
            $line .= '-' . Helper::strPad('-', $opts['size'], '-') . '-+';
        }
        $this->output->write($line, true);
    }

    protected function header()
    {
        $closure = $this->style === self::NO_STYLE ? '' : '|';
        $line    = $closure;
        foreach ($this->columns as $column => $opts) {
            $line .= ' ' . Helper::strPad(str_upper($column), $opts['size'], ' ') . ' ' . $closure;
        }
        $this->output->write($line, true);
    }

    public function display($header = true)
    {
        $this->generateColumns();

        $this->separator();

        if ($header) {
            $this->header();
        }

        $this->separator();

        $closure = $this->style === self::NO_STYLE ? '' : '|';
        foreach ($this->datas as $data) {
            $line = $closure;
            foreach ($this->columns as $column => $opts) {
                $line .= ' ' . Helper::strPad(arr_fetch($data, $column, ''), $opts['size'], ' ') . ' ' . $closure;
            }
            $this->output->write($line, true);
        }

        $this->separator();
    }
}
