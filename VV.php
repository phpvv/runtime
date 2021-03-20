<?php declare(strict_types=1);

/*
 * This file is part of the VV package.
 *
 * (c) Volodymyr Sarnytskyi <v00v4n@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace VV;

/**
 * Class VV
 *
 * @package VV
 */
final class VV {

    private ?string $appName = null;
    private ?bool $testMode = null;
    private ?bool $devMode = null;
    private ?string $serverId = null;
    private ?string $assetsVersion = null;
    private ?string $appPath = null;
    private ?string $tmpPath = null;
    private ?string $storePath = null;
    private ?string $htdocsPath = null;

    public function init(string $appName, $testMode = null, $devMode = null): self {
        $this->appName = $appName;
        $this->testMode = $testMode;
        $this->devMode = $devMode;

        return $this;
    }

    /**
     * @return string
     */
    public function appName(): string {
        if ($this->appName === null) {
            $this->appName = getenv('VV_APP_NAME') ?: 'VVebApp';
        }

        return $this->appName;
    }

    /**
     * @return string
     * @deprecated Use {@see appName()}
     */
    public function name(): string {
        return $this->appName();
    }

    /**
     * @return bool
     */
    public function testMode(): bool {
        if ($this->testMode === null) {
            $env = getenv('VV_TEST_MODE');
            $this->testMode = $env !== false ? (bool)$env : true;
        }

        return $this->testMode;
    }

    /**
     * @return bool
     */
    public function devMode(): bool {
        if ($this->devMode === null) {
            $env = getenv('VV_DEV_MODE');
            $this->devMode = $env !== false ? (bool)$env : false;
        }

        return $this->devMode;
    }

    /**
     * @return string
     */
    public function serverId(): string {
        if ($this->serverId === null) $this->serverId = getenv('VV_SERVER_ID') ?: '';

        return $this->serverId;
    }

    /**
     * @return string
     */
    public function appPath(): string {
        if ($this->appPath === null) $this->appPath =  dirname(__DIR__, 3);

        return $this->appPath;
    }

    /**
     * @param string|null $appPath
     *
     * @return $this
     */
    public function setAppPath(?string $appPath): self {
        $this->appPath = $appPath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function tmpPath(): ?string {
        return $this->tmpPath;
    }

    /**
     * @param string|null $tmpPath
     *
     * @return $this
     */
    public function setTmpPath(?string $tmpPath): self {
        $this->tmpPath = $tmpPath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function storePath(): ?string {
        return $this->storePath;
    }

    /**
     * @param string|null $storePath
     *
     * @return $this
     */
    public function setStorePath(?string $storePath): self {
        $this->storePath = $storePath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function htdocsPath(): ?string {
        return $this->htdocsPath;
    }

    /**
     * @param string|null $htdocsPath
     *
     * @return $this
     */
    public function setHtdocsPath(?string $htdocsPath): self {
        $this->htdocsPath = $htdocsPath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function assetsVersion(): ?string {
        return $this->assetsVersion;
    }

    /**
     * @param string|null $assetsVersion
     *
     * @return $this
     */
    public function setAssetsVersion(?string $assetsVersion): self {
        $this->assetsVersion = $assetsVersion;

        return $this;
    }
}
